<?php

namespace App\Controller\Registration;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnVolunterAvailability;
use App\Entity\Transaction\TrnVolunterDetail;
use App\Entity\Transaction\TrnVolunterInterest;
use App\Form\SystemApp\AppUserRegistrationEditType;
use App\Form\SystemApp\AppUserRegistrationType;
use App\Repository\Master\MstAreaInterestRepository;
use App\Repository\SystemApp\AppUserInfoRepository;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use App\Service\FileUploaderHelper;
use App\Service\Mailer;
use App\Service\MyAccountService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/core/registration/register", name="registration_register_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class RegisterController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppUserInfoRepository $appUserInfoRepository
     * @return Response
     */
    public function index(AppUserInfoRepository $appUserInfoRepository): Response
    {
        $objAppUserCategory = $this->getDoctrine()->getRepository(AppUserCategory::class)->findOneBy(['userCategory' => 'Application', 'isActive' => 1]);
        return $this->render('registration/register/index.html.twig', [
            'users' => $appUserInfoRepository->getOnlyApplicationUsers($objAppUserCategory),
            'path_index' => 'registration_register_index',
            'path_add' => 'registration_register_add',
            'path_edit' => 'registration_register_edit',
            'path_show' => 'registration_register_show',
            'path_upload' => 'registration_register_upload',
            'label_title' => 'label.register',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @param MstAreaInterestRepository $mstAreaInterestRepository
     * @param MyAccountService $myAccountService
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, /*Mailer $mailer,*/ FileUploaderHelper $fileUploaderHelper,
                        MstAreaInterestRepository $mstAreaInterestRepository,
                        MyAccountService $myAccountService, TrnVolunterInterestRepository $trnVolunterInterestRepository,
                        UserPasswordEncoderInterface $encoder): Response
    {
        $appUser = new AppUser();

        $mstAreaInterestArr = $mstAreaInterestRepository->findBy(['isActive' => 1, 'mstAreaInterestPrimary' => null],
            ['sequenceNo' => 'ASC']);
        $userAreaInterestArr = $myAccountService->getUserAreaOfInterest($appUser);

        $arrAvailability = array();
        $arrVolunteerDocuments = array();

        $form = $this->createForm(AppUserRegistrationType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($appUser, $appUser->getUserName());
            $appUser->setUserPassword($password);
            $token = bin2hex(random_bytes(32));
            $appUser->setRowId(Uuid::uuid4()->toString());
            $appRole = array('ROLE_B2C_USER');
            $appUser->setRoles($appRole);
            $appUser->setUserCreationDateTime(new DateTime('now'));
            $appUser->setUserCreationToken($token);
            $appUser->getAppUserInfo()->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $objAppUserCategory = $this->getDoctrine()->getRepository(AppUserCategory::class)->findOneBy(['userCategory' => 'Application', 'isActive' => 1]);
            $appUser->setAppUserCategory($objAppUserCategory);
            $entityManager = $this->getDoctrine()->getManager();

            $primaryArea = $request->get('primaryArea');
            $secondaryArea = $request->get('secondaryArea');

            $availableInMorning = $request->get('availableInMorning', array());
            $availableInAfternoon = $request->get('availableInAfternoon', array());
            $availableInEvening = $request->get('availableInEvening', array());

            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();

            if (empty($objTrnVolunterDetail)){
                $objTrnVolunterDetail = new TrnVolunterDetail();
            } else {
                $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                    $objTrnVolunterDetail, 'isActive' => 1));
            }

            //Remove Previous Entries if any
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                $entityManager->remove($trnVolunterInterest);
            }
            $entityManager->flush();

            //Add new entries
            foreach ($primaryArea as $nPrimaryAI) {
                $objTrnVolunterInterest = new TrnVolunterInterest();
                $objTrnVolunterInterest->setIsActive(1);
                $objTrnVolunterInterest->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterInterest->setAppUser($appUser);
                $objTrnVolunterInterest->setCreatedOn(new \DateTime());
                $objTrnVolunterInterest->setTrnVolunterDetail($objTrnVolunterDetail);

                $objTrnVolunterInterest->setAreaInterestPrimary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nPrimaryAI));
                foreach ($secondaryArea[$nPrimaryAI] as $nSecAI) {
                    $objTrnVolunterInterest->addAreaInterestSecondary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI));
                }
                $entityManager->persist($objTrnVolunterInterest);
            }
            $entityManager->flush();

            foreach ($availableInMorning as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('morning');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }
            foreach ($availableInAfternoon as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('afternoon');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }
            foreach ($availableInEvening as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('evening');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }

            foreach ($appUser->getTrnOrganizationDetails() as
                     $trnOrganizationDetails){
                $trnOrganizationDetails->setAppUser($appUser);
                $trnOrganizationDetails->setOrgCompany($appUser->getAppUserInfo()->getOrgCompany());
                $file = $request->files->get('app_user_registration')['trnOrganizationDetails'][0]['organizationLogo'];
                if ($file) {
                    if ($trnOrganizationDetails->getOrganizationLogo() != '') {
                        $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $trnOrganizationDetails->getOrganizationLogo());
                    } else {
                        $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $logo = null);
                    }
                    $trnOrganizationDetails->setOrganizationLogo($newFilename);
                    $trnOrganizationDetails->setOrganizationLogoFilePath($this->getParameter('public_file_folder'));
                }
                $trnOrganizationDetails->setCreatedOn(new DateTime());
                $trnOrganizationDetails->setIsActive(true);
                foreach ($trnOrganizationDetails->getTrnBankDetails() as $trnBankDetails) {
                    $trnBankDetails->setIsActive(true);
                    $trnBankDetails->setCreatedOn(new DateTime());
                }
            }
            #Send Reset Password Email
            $tokenExpiryTime = new DateTime('+24 hour');
            $userName = $appUser->getUserName();
            $userEmail = $appUser->getAppUserInfo()->getUserEmail();
            $appUser->setUserResetPasswordToken($token);
            $appUser->setUserResetPasswordTokenExpiry($tokenExpiryTime);
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            // Send Reset Email to User
            //$mailer->mailerResetPassword($userEmail, $userName, $token);
            #Send Reset Password Email
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('registration_register_index');
        }
        return $this->render('registration/register/form.html.twig', [
            'user' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.register',
            'label_button' => 'label.create',
            'mode' => 'add',
            'mstAreaInterestArr' => $mstAreaInterestArr,
            'arrSecondaryAI' => $userAreaInterestArr['secondary'],
            'arrPrimaryAI' => $userAreaInterestArr['primary'],
            'arrAvailability' => $arrAvailability,
            'arrVolunteerDocuments' => $arrVolunteerDocuments
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppUser $appUser
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function edit(Request $request, AppUser $appUser, TrnVolunterInterestRepository $trnVolunterInterestRepository): Response
    {
        $form = $this->createForm(AppUserRegistrationEditType::class, $appUser);

        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        $trnVolunterInterestArr = array();
        if (!empty($objTrnVolunterDetail)){
            $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($appUser->getTrnBankDetails() as $trnBankDetails) {
                $trnBankDetails->setIsActive(true);
                $trnBankDetails->setCreatedOn(new DateTime());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUser);
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('registration_register_index');
        }

        return $this->render('registration/register/edit_form.html.twig', [
            'data' => $appUser,
            'form' => $form->createView(),
            'mode' => 'edit',
            'label_title' => 'label.register',
            'label_button' => 'label.update',
            'path_index' => 'registration_register_index',
            'path_edit' => 'registration_register_edit',
            'trnVolunterInterestArr' => $trnVolunterInterestArr
        ]);
    }

    /**
     * @Route("/edit_old/{id}", name="edit_old", methods={"GET","POST"})
     * @param Request $request
     * @param AppUser $appUser
     * @param FileUploaderHelper $fileUploaderHelper
     * @param MstAreaInterestRepository $mstAreaInterestRepository
     * @param MyAccountService $myAccountService
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     * @throws \Exception
     */
    public function editOld(Request $request, AppUser $appUser, FileUploaderHelper $fileUploaderHelper,
                         MstAreaInterestRepository $mstAreaInterestRepository,
                         MyAccountService $myAccountService, TrnVolunterInterestRepository $trnVolunterInterestRepository): Response
    {
        $form = $this->createForm(AppUserRegistrationType::class, $appUser);

        $mstAreaInterestArr = $mstAreaInterestRepository->findBy(['isActive' => 1, 'mstAreaInterestPrimary' => null],
            ['sequenceNo' => 'ASC']);
        $userAreaInterestArr = $myAccountService->getUserAreaOfInterest($appUser);

        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        $arrAvailability = array();
        $arrVolunteerDocuments = array();

        if(!empty($objTrnVolunterDetail)) {
            foreach ($objTrnVolunterDetail->getTrnVolunterAvailabilities() as $trnVolunterAvailability) {
                $arrAvailability[ucfirst($trnVolunterAvailability->getAvailableOnTime())
                ][$trnVolunterAvailability->getAvailableOnDay()] = strtoupper(substr($trnVolunterAvailability->getAvailableOnDay(), 0, 3))  ;
            }
            foreach ($objTrnVolunterDetail->getTrnVolunterAvailabilities() as $trnVolunterAvailability) {
                $arrAvailability[ucfirst($trnVolunterAvailability->getAvailableOnTime())
                ][$trnVolunterAvailability->getAvailableOnDay()] = strtoupper(substr($trnVolunterAvailability->getAvailableOnDay(), 0, 3))  ;
            }
            foreach ($objTrnVolunterDetail->getTrnVolunteerDocuments() as $trnVolunteerDocument) {
                $arrVolunteerDocuments[] = $trnVolunteerDocument->getUploadedFilePath();
            }
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $appUser->getAppUserInfo()->setUserIpAddress($_SERVER['SERVER_ADDR']);
            //$appUser->setUserPassword($appUser->getUserName());
            $entityManager = $this->getDoctrine()->getManager();

            $primaryArea = $request->get('primaryArea');
            $secondaryArea = $request->get('secondaryArea');

            $availableInMorning = $request->get('availableInMorning', array());
            $availableInAfternoon = $request->get('availableInAfternoon', array());
            $availableInEvening = $request->get('availableInEvening', array());

            // remove old trn area of interest and add new area of interest for user
            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
            if (empty($objTrnVolunterDetail)){
                $objTrnVolunterDetail = new TrnVolunterDetail();
            } else {
                $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                    $objTrnVolunterDetail, 'isActive' => 1));
            }
            //Remove Previous Entries if any
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                $entityManager->remove($trnVolunterInterest);
            }
            $entityManager->flush();

            //Add new entries
            foreach ($primaryArea as $nPrimaryAI) {
                $objTrnVolunterInterest = new TrnVolunterInterest();
                $objTrnVolunterInterest->setIsActive(1);
                $objTrnVolunterInterest->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterInterest->setAppUser($appUser);
                $objTrnVolunterInterest->setCreatedOn(new \DateTime());
                $objTrnVolunterInterest->setTrnVolunterDetail($objTrnVolunterDetail);

                $objTrnVolunterInterest->setAreaInterestPrimary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nPrimaryAI));
                foreach ($secondaryArea[$nPrimaryAI] as $nSecAI) {
                    $objTrnVolunterInterest->addAreaInterestSecondary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI));
                }
                $entityManager->persist($objTrnVolunterInterest);
            }
            $entityManager->flush();

            $existingTrnVolunterAvailability = $entityManager->getRepository(TrnVolunterAvailability::class)->findBy(array('appUser' =>
                $appUser, 'isActive' => 1));
            foreach ($existingTrnVolunterAvailability as $trnVolunteerAvailability) {
                $objTrnVolunterDetail->removeTrnVolunterAvailability($trnVolunteerAvailability);
            }
            $entityManager->persist($objTrnVolunterDetail);
            $entityManager->flush();

            foreach ($availableInMorning as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('morning');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }
            foreach ($availableInAfternoon as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('afternoon');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }
            foreach ($availableInEvening as $day) {
                $objTrnVolunterAvailability = new TrnVolunterAvailability();
                $objTrnVolunterAvailability->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterAvailability->setCreatedOn(new \DateTime());
                $objTrnVolunterAvailability->setAppUser($appUser);
                $objTrnVolunterAvailability->setIsActive(1);
                $objTrnVolunterAvailability->setAvailableOnDay($day);
                $objTrnVolunterAvailability->setAvailableOnTime('evening');
                $entityManager->persist($objTrnVolunterAvailability);
                $objTrnVolunterDetail->addTrnVolunterAvailability($objTrnVolunterAvailability);
            }
            $objTrnVolunterDetail->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')) );
            $objTrnVolunterDetail->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager->persist($objTrnVolunterDetail);
            $entityManager->flush();

            foreach ($appUser->getTrnOrganizationDetails() as  $trnOrganizationDetails){
                $file = $request->files->get('app_user_registration')['trnOrganizationDetails'][0]['organizationLogo'];
                if ($file) {
                    if ($trnOrganizationDetails->getOrganizationLogo() != '') {
                        $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $trnOrganizationDetails->getOrganizationLogo());
                    } else {
                        $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $logo = null);
                    }
                    $trnOrganizationDetails->setOrganizationLogo($newFilename);
                    $trnOrganizationDetails->setOrganizationLogoFilePath($this->getParameter('public_file_folder'));
                }
                $trnOrganizationDetails->setAppUser($appUser);
                $trnOrganizationDetails->setOrgCompany($appUser->getAppUserInfo()->getOrgCompany());
                $trnOrganizationDetails->setCreatedOn(new DateTime());
                $trnOrganizationDetails->setIsActive(true);
                foreach ($appUser->getTrnBankDetails() as $trnBankDetails) {
                    $trnBankDetails->setIsActive(true);
                    $trnBankDetails->setCreatedOn(new DateTime());
                }
            }
            $entityManager->persist($appUser);
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('registration_register_index');
        }
        return $this->render('registration/register/form.html.twig', [
            'user' => $appUser,
            'form' => $form->createView(),
            'label_title' => 'label.register',
            'label_button' => 'label.update',
            'mode' => 'edit',
            'mstAreaInterestArr' => $mstAreaInterestArr,
            'arrSecondaryAI' => $userAreaInterestArr['secondary'],
            'arrPrimaryAI' => $userAreaInterestArr['primary'],
            'arrAvailability' => $arrAvailability,
            'arrVolunteerDocuments' => $arrVolunteerDocuments
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     * @param AppUser $appUser
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function show(AppUser $appUser, TrnVolunterInterestRepository $trnVolunterInterestRepository): Response
    {
        $objTrnVolunterDetail = $appUser->getTrnVolunterDetail();
        $trnVolunterInterestArr = array();
        if (!empty($objTrnVolunterDetail)) {
            $trnVolunterInterestArr = $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
        }
        return $this->render('registration/register/show.html.twig', [
            'data' => $appUser,
            'label_title' => 'label.register',
            'label_button' => 'label.update',
            'path_index' => 'registration_register_index',
            'path_edit' => 'registration_register_edit',
            'trnVolunterInterestArr' => $trnVolunterInterestArr
        ]);
    }
}