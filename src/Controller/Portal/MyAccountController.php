<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstState;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnAppUserContacts;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use App\Entity\Transaction\TrnVolunterAvailability;
use App\Entity\Transaction\TrnVolunterDetail;
use App\Entity\Transaction\TrnVolunteerDocument;
use App\Entity\Transaction\TrnVolunterInterest;
use App\Form\SystemApp\AppUserMyAccountAboutType;
use App\Form\SystemApp\AppUserOrganizationMyAccountAboutType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\Master\MstAreaInterestRepository;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstSalutationRepository;
use App\Repository\Master\MstSkillSetRepository;
use App\Repository\Master\MstSourceOfInformationRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\Master\MstUploadDocumentTypeRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnAppUserContactsRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use App\Service\FileUploaderHelper;
use App\Service\Mailer;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MyAccountController
 * @IsGranted("ROLE_APP_USER")
 */
class MyAccountController extends AbstractController
{
    /**
     * @var array
     */
    private $arrSocialProfileData;

    /**
     * @Route("/my-account/personal-info", name="personal-info", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param SessionInterface $session
     * @return Response
     */
    public function personalInfo(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, SessionInterface $session)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $session->remove('areaOfInterest');
        if($profileCompleteness < 90){
            return $this->redirectToRoute('edit-personal-info');
        } else {
            return $this->redirectToRoute('personal-info-first-time-user-edit');
        }
    }

    /**
     * @Route("/my-account/edit-personal-info", name="edit-personal-info", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param MstSourceOfInformationRepository $mstSourceOfInformationRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @param SessionInterface $session
     * @return Response
     * @throws \Exception
     */
    public function editPersonalInfo(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, MstSourceOfInformationRepository $mstSourceOfInformationRepository, FileUploaderHelper
    $fileUploaderHelper, SessionInterface $session)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $session->remove('areaOfInterest');
        $appUserInfo = $appUser->getAppUserInfo();
        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        if ($strMemberType == 'Organization') {
            return $this->redirectToRoute('edit-organization-info');
        }
        $trnBankDetailsArr = $appUser->getTrnBankDetails();
        $trnBankDetail = null;
        if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
            $trnBankDetail = $trnBankDetailsArr[0];
        } else {
            $trnBankDetail = new TrnBankDetails();
            $appUser->addTrnBankDetail($trnBankDetail);
        }
        $form = $this->createForm(AppUserMyAccountAboutType::class, $appUser);
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $form->handleRequest($request);
        $arrSourceOfInformation = $mstSourceOfInformationRepository->findBy(['isActive' => 1]);
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (empty($objTrnVolunterDetail))
            $objTrnVolunterDetail = new TrnVolunterDetail();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $objAppUserInfo =  $appUser->getAppUserInfo();
            $entityManager = $this->getDoctrine()->getManager();
            $objMstUserMemberType = $entityManager->getRepository(MstUserMemberType::class)->findOneBy(["userMemberType" => 'Individual']);
            $objAppUserInfo->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objAppUserInfo->setMstUserMemberType($objMstUserMemberType);
            $objAppUserInfo->setMobileCountryCode('+91');
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            $entityManager = $this->getDoctrine()->getManager();
            $arrImageGallery = $request->files->get('imageDocGallery');
            $availabilityInMorning  = $request->get('hdnAvailabilityInMorning');
            $availabilityInAfternoon  = $request->get('hdnAvailabilityInAfternoon');
            $availabilityInEvening  = $request->get('hdnAvailabilityInEvening');

            $arrMorningAvailableDays = explode(",",$availabilityInMorning);
            $arrAfternoonAvailableDays = explode(",",$availabilityInAfternoon);
            $arrEveningAvailableDays = explode(",",$availabilityInEvening);

            // remove old if any
            $existingTrnVolunterAvailability = $entityManager->getRepository(TrnVolunterAvailability::class)->findBy(array('appUser' =>
                $appUser, 'isActive' => 1));
            foreach ($existingTrnVolunterAvailability as $trnVolunteerAvailability) {
                $objTrnVolunterDetail->removeTrnVolunterAvailability($trnVolunteerAvailability);
            }
            $entityManager->persist($objTrnVolunterDetail);
            $entityManager->flush();

            foreach ($arrMorningAvailableDays as $day) {
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
            foreach ($arrAfternoonAvailableDays as $day) {
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
            foreach ($arrEveningAvailableDays as $day) {
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
            $objTrnVolunterDetail->setCreatedOn(new \DateTime());
            $entityManager->persist($objTrnVolunterDetail);

            foreach ($arrImageGallery as $key => $objImageGallery) {
                if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                    $objImageGallery = $objImageGallery[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'Document'
                        .$objTrnVolunterDetail->getId().$key.Uuid::uuid4()->toString(), null);
                    $objTrnVolunteerDocument = new TrnVolunteerDocument();
                    $objTrnVolunteerDocument->setUploadedFilePath($newFilename);
                    $objTrnVolunteerDocument->setTrnVolunterDetail($objTrnVolunterDetail);
                    $objTrnVolunteerDocument->setIsActive(1);
                    $objTrnVolunteerDocument->setUploadedOn(new \DateTime());
                    $objTrnVolunteerDocument->setAppUser($appUser);
                    $entityManager->persist($objTrnVolunteerDocument);
                    $objTrnVolunterDetail->addTrnVolunteerDocument($objTrnVolunteerDocument);
                }
            }
            $entityManager->flush();
            //Check if Bank Details Exists
            if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
                $trnBankDetail = $trnBankDetailsArr[0];
                if (empty($trnBankDetail->getAccountHolderName()) &&
                    empty($trnBankDetail->getAccountNumber()) &&
                    empty($trnBankDetail->getIfscCode()) &&
                    empty($trnBankDetail->getMstBankAccountType())){
                    $appUser->removeTrnBankDetail($trnBankDetail);
                    $entityManager->persist($appUser);
                    $entityManager->flush();
                }
            }
            //Check if Bank Details Exists
            return $this->redirectToRoute('profile-info-skills');
        }
        $arrMstSourceOfInformation = $arrAvailability = $arrVolunteerDocuments = array();
        if (!empty($objTrnVolunterDetail)) {
            foreach ($objTrnVolunterDetail->getMstSourceOfInformation() as $mstSourceOfInformation) {
                $arrMstSourceOfInformation[$mstSourceOfInformation->getId()] = $mstSourceOfInformation->getId();
            }
            foreach ($objTrnVolunterDetail->getTrnVolunterAvailabilities() as $trnVolunterAvailability) {
                $arrAvailability[$trnVolunterAvailability->getAvailableOnTime()
                ][$trnVolunterAvailability->getAvailableOnDay()] = $trnVolunterAvailability->getAvailableOnDay();
            }
            foreach ($objTrnVolunterDetail->getTrnVolunteerDocuments() as $trnVolunteerDocument) {
                $arrVolunteerDocuments[] = $trnVolunteerDocument->getUploadedFilePath();
            }
        }
        return $this->render('portal/my-account/personal-info/personal-info.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'form' => $form->createView(),
            'arrSourceOfInformation' => $arrSourceOfInformation, 'objTrnVolunterDetail' => $objTrnVolunterDetail,
            'arrMstSourceOfInformation' => $arrMstSourceOfInformation, 'arrAvailability' => $arrAvailability,
            'arrVolunteerDocuments' => $arrVolunteerDocuments, 'trnBankDetail' => $trnBankDetail
        ]);
    }

    /**
     * @Route("/my-account/profile-info-skills", name="profile-info-skills", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param MstSkillSetRepository $mstSkillSetRepository
     * @return Response
     */
    public function personalInfoSkills(Request $request, TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, MstSkillSetRepository $mstSkillSetRepository) : Response{
        $mstSkillSetArr = $mstSkillSetRepository->findBy(['isActive' => 1, ]);
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $mstSkillSetCountArr = $myAccountService->getSkillSetCounts();
        $appUserInfo = $appUser->getappUserInfo();
        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        $bIsOrganization = false;
        if ($strMemberType == 'Organization') {
            $bIsOrganization = true;
        }
        if($request->isMethod('POST')){
            $entityManager = $this->getDoctrine()->getManager();
            $skillSet = $request->get('skillSet');
            foreach ($appUserInfo->getMstSkillSet() as $mstSkillToRemove) {
                $appUserInfo->removeMstSkillSet($mstSkillToRemove);
            }
            $entityManager->persist($appUserInfo);
            $entityManager->flush();
            foreach ($skillSet as $id) {
                $mstSkillSet = $mstSkillSetRepository->find($id);
                $appUserInfo->addMstSkillSet($mstSkillSet);
            }
            $entityManager->persist($appUserInfo);
            $entityManager->flush();
            return $this->redirectToRoute('personal-info-interest-area');
        }
        $arrMstSkillSet = array();
        $arrTemp = $appUserInfo->getMstSkillSet();
        foreach ($arrTemp as $mstSkillSet) {
            $arrMstSkillSet[] = $mstSkillSet->getId();
        }
        return $this->render('portal/my-account/personal-info/profile-info-skills.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'mstSkillSetArr' => $mstSkillSetArr,
            'arrMstSkillSet' => $arrMstSkillSet, 'mstSkillSetCountArr' => $mstSkillSetCountArr, 'bIsOrganization' =>
                $bIsOrganization
        ]);
    }

    /**
     * @Route("/my-account/personal-info-interest-area", name="personal-info-interest-area", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param MstAreaInterestRepository $mstAreaInterestRepository
     * @param SessionInterface $session
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function personalInfoInterestArea(Request $request, TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, MstAreaInterestRepository $mstAreaInterestRepository, SessionInterface $session,
                                             TrnVolunterInterestRepository $trnVolunterInterestRepository) : Response{
        $mstAreaInterestArr = $mstAreaInterestRepository->findBy(['isActive' => 1, 'mstAreaInterestPrimary' => null],
            ['sequenceNo' => 'ASC']);
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $mstAreaInterestCountArr = $myAccountService->getAreaInterestCount();
        $appUser = $tokenStorage->getToken()->getUser();
        $appUserInfo = $appUser->getappUserInfo();
        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        $bIsOrganization = false;
        if ($strMemberType == 'Organization') {
            $bIsOrganization = true;
        }
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (empty($objTrnVolunterDetail)){
            $objTrnVolunterDetail = new TrnVolunterDetail();
        } else {
            $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
        }
        $arrPrimaryAISecAI = $arrSecondaryAI = $arrPrimaryAI = array();
        if($request->isMethod('POST')){
            $arrAreaOfInterest = $session->get('areaOfInterest', array());
            $entityManager = $this->getDoctrine()->getManager();
            //Remove Previous Entries if any
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                $entityManager->remove($trnVolunterInterest);
            }
            $entityManager->flush();
            //Remove Previous Entries if any
            foreach ($arrAreaOfInterest as $nPrimaryAI => $arrSecondaryAI) {
                $objTrnVolunterInterest = new TrnVolunterInterest();
                $objTrnVolunterInterest->setIsActive(1);
                $objTrnVolunterInterest->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnVolunterInterest->setAppUser($appUser);
                $objTrnVolunterInterest->setCreatedOn(new \DateTime());
                $objTrnVolunterInterest->setTrnVolunterDetail($objTrnVolunterDetail);

                $objTrnVolunterInterest->setAreaInterestPrimary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nPrimaryAI));
                foreach ($arrSecondaryAI as  $key => $nSecAI) {
                    $objTrnVolunterInterest->addAreaInterestSecondary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI));
                }
                $entityManager->persist($objTrnVolunterInterest);
            }
            $entityManager->flush();
            $session->remove('areaOfInterest');
            return $this->redirectToRoute('personal-info-first-time-user-edit');
        } else {
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                if (!empty($trnVolunterInterest->getAreaInterestPrimary())) {
                    $arrPrimaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()] =
                        $trnVolunterInterest->getAreaInterestPrimary();
                    foreach ($trnVolunterInterest->getAreaInterestSecondary() as $secAI) {
                        $arrSecondaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()][$secAI->getId()] = $secAI;
                        $arrPrimaryAISecAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()][] = $secAI->getId();
                    }
                }
            }
            if (!empty($arrPrimaryAISecAI))
                $session->set('areaOfInterest', $arrPrimaryAISecAI);
        }
        return $this->render('portal/my-account/personal-info/personal-info-interest-area.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'mstAreaInterestArr' => $mstAreaInterestArr,
            'arrSecondaryAI' => $arrSecondaryAI, 'arrPrimaryAI' => $arrPrimaryAI, 'mstAreaInterestCountArr' =>
                $mstAreaInterestCountArr, 'bIsOrganization' => $bIsOrganization
        ]);
    }


    /**
     * @Route("/my-account/personal-info-first-time-user-edit", name="personal-info-first-time-user-edit", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function personalInfoFirstTimeUserEdit(Request $request, TokenStorageInterface $tokenStorage,
                                                  MyAccountService $myAccountService,
                                                  TrnVolunterInterestRepository $trnVolunterInterestRepository) : Response{
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $appUserInfo = $appUser->getAppUserInfo();
        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        if ($strMemberType == 'Organization') {
            return $this->redirectToRoute('organization-info-first-time-user-edit');
        }
        $trnBankDetailsArr = $appUser->getTrnBankDetails();
        $trnBankDetail = null;
        if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
            $trnBankDetail = $trnBankDetailsArr[0];
        }
        $arrVolunteerDocuments = $arrAvailability = $arrPrimaryAI = $arrSecondaryAI = array();
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (!empty($objTrnVolunterDetail)) {
            $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                if (!empty($trnVolunterInterest->getAreaInterestPrimary())) {
                    $arrPrimaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()] =
                        $trnVolunterInterest->getAreaInterestPrimary();
                    foreach ($trnVolunterInterest->getAreaInterestSecondary() as $secAI) {
                        $arrSecondaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()][$secAI->getId()] = $secAI;
                    }
                }
            }
            foreach ($objTrnVolunterDetail->getTrnVolunterAvailabilities() as $trnVolunterAvailability) {
                $arrAvailability[ucfirst($trnVolunterAvailability->getAvailableOnTime())
                ][$trnVolunterAvailability->getAvailableOnDay()] = strtoupper(substr($trnVolunterAvailability->getAvailableOnDay(), 0, 3))  ;
            }
            foreach ($objTrnVolunterDetail->getTrnVolunteerDocuments() as $trnVolunteerDocument) {
                $arrVolunteerDocuments[] = $trnVolunteerDocument->getUploadedFilePath();
            }
        }
        return $this->render('portal/my-account/personal-info/personal-info-first-time-user-edit.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'trnVolunterDetail' =>
                $objTrnVolunterDetail, 'appUserInfo' => $appUser->getAppUserInfo(), 'trnBankDetails' => $trnBankDetail,
            'arrPrimaryAI' => $arrPrimaryAI, 'arrSecondaryAI' => $arrSecondaryAI, 'arrAvailability' => $arrAvailability,
            'arrVolunteerDocuments' => $arrVolunteerDocuments
        ]);
    }

    /**
     * @Route("/my-account/profile-steps", name="profile-steps", methods={"GET", "POST"})
     * @param Request $request
     * @param string $active
     * @return Response
     */
    public function myAccountProfileSteps(Request $request, $active = ''){
        return $this->render('portal/my-account/personal-info/profile-steps.html.twig', [
            'active'=> $active
        ]);
    }

    /**
     * @Route("/my-account/sidebar-navigation", name="sidebar-navigation", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function sidebarNavigation(Request $request) :Response
    {
        return $this->render('portal/my-account/sidebar-navigation.html.twig', [

        ]);
    }

    /**
     * @Route("/my-account/my-account-banner", name="my-account-banner", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function myAccountBanner(Request $request,  TokenStorageInterface $tokenStorage) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        return $this->render('portal/my-account/my-account-banner.html.twig', [
            'appUser'=> $appUser
        ]);
    }

    #############################################   Personal Information ######################################

    #############################################   Organization Information ######################################

    /**
     * @Route("/my-account/edit-organization-info", name="edit-organization-info", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param FileUploaderHelper $fileUploaderHelper
     * @param SessionInterface $session
     * @param MstUploadDocumentTypeRepository $mstUploadDocumentTypeRepository
     * @return Response
     * @throws \Exception
     */
    public function editOrganizationInfo(Request $request, TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, FileUploaderHelper $fileUploaderHelper, SessionInterface $session,
                                         MstUploadDocumentTypeRepository $mstUploadDocumentTypeRepository) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $session->remove('areaOfInterest');
        $trnBankDetailsArr = $appUser->getTrnBankDetails();
        $trnBankDetail = null;
        if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
            $trnBankDetail = $trnBankDetailsArr[0];
        } else {
            $trnBankDetail = new TrnBankDetails();
            $appUser->addTrnBankDetail($trnBankDetail);
        }
        $form = $this->createForm(AppUserOrganizationMyAccountAboutType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $objAppUserInfo =  $appUser->getAppUserInfo();
            $entityManager = $this->getDoctrine()->getManager();
            $objAppUserInfo->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objAppUserInfo->setMobileCountryCode('+91');
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            $entityManager = $this->getDoctrine()->getManager();
            $arrImageGallery = $request->files->get('imageDocGallery');
            $organizationLogo = $request->files->get('organizationLogo');
            $mstUploadDocumentType = $mstUploadDocumentTypeRepository->findOneBy(['uploadDocumentType' => 'Image']);
            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
            if(!empty($organizationLogo) && !empty($organizationLogo[0])) {
                $organizationLogo = $organizationLogo[0];
                $newFilename = $fileUploaderHelper->uploadPublicFile($organizationLogo, 'Logo' . Uuid::uuid4()->toString(), null);
                $trnOrganizationDetails = $appUser->getTrnOrganizationDetails();
                if (!empty($trnOrganizationDetails) && !empty($trnOrganizationDetails[0])) {
                    $trnOrganizationDetails = $trnOrganizationDetails[0];
                    $trnOrganizationDetails->setLogoFilePath($newFilename);
                    $entityManager->persist($trnOrganizationDetails);
                }
            } else {
                $trnOrganizationDetails = $appUser->getTrnOrganizationDetails();
                if (!empty($trnOrganizationDetails) && !empty($trnOrganizationDetails[0])) {
                    $trnOrganizationDetails = $trnOrganizationDetails[0];
                    $trnOrganizationDetails->setLogoFilePath('');
                    $entityManager->persist($trnOrganizationDetails);
                }
            }
            if (empty($objTrnVolunterDetail)){
                $objTrnVolunterDetail = new TrnVolunterDetail();
                $appUser->setTrnVolunterDetail($objTrnVolunterDetail);
                $entityManager->persist($appUser);
            }
            foreach ($arrImageGallery as $key => $objImageGallery) {
                if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                    $objImageGallery = $objImageGallery[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'Document'.$key.Uuid::uuid4()->toString(), null);
                    $trnOrganizationUploadDocument = new TrnOrganizationUploadDocuments();
                    $trnOrganizationUploadDocument->setIsActive(1);
                    $trnOrganizationUploadDocument->setAppUser($appUser);
                    $trnOrganizationUploadDocument->setCreatedOn(new \DateTime());
                    $trnOrganizationUploadDocument->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnOrganizationUploadDocument->setUserIpAddress($_SERVER['SERVER_ADDR']);
                    $trnOrganizationUploadDocument->setUploadDocumentPath($newFilename);
                    $trnOrganizationUploadDocument->setMediaAltText(strtoupper(pathinfo($objImageGallery->getClientOriginalName(),PATHINFO_FILENAME)));
                    $trnOrganizationUploadDocument->setMediaFileName($newFilename);
                    $trnOrganizationUploadDocument->setMediaName(strtoupper(pathinfo($objImageGallery->getClientOriginalName(),PATHINFO_FILENAME)));
                    $trnOrganizationUploadDocument->setMediaTitle(strtoupper(pathinfo($objImageGallery->getClientOriginalName(),PATHINFO_FILENAME)));
                    $trnOrganizationUploadDocument->setMstUploadDocumentType($mstUploadDocumentType);
                    $appUser->addTrnOrganizationUploadDocument($trnOrganizationUploadDocument);
                    $entityManager->persist($trnOrganizationUploadDocument);
                    $entityManager->persist($appUser);
                }
            }
            $entityManager->flush();
            return $this->redirectToRoute('profile-info-skills');
        }
        $arrVolunteerDocuments = array();
        foreach ($appUser->getTrnOrganizationUploadDocuments() as $trnVolunteerDocument) {
            $arrVolunteerDocuments[] = $trnVolunteerDocument->getUploadDocumentPath();
        }
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $arrTrnOrganizationDetails = $appUser->getTrnOrganizationDetails();
        $trnOrganizationDetails = null;
        if (!empty($arrTrnOrganizationDetails) && !empty($arrTrnOrganizationDetails[0])) {
            $trnOrganizationDetails = $arrTrnOrganizationDetails[0];
        }
        return $this->render('portal/my-account/organization/organization-info.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'form' => $form->createView(),
            'trnBankDetail' => $trnBankDetail, 'arrVolunteerDocuments' => $arrVolunteerDocuments,
            'trnOrganizationDetails' => $trnOrganizationDetails
        ]);
    }

    /**
     * @Route("/my-account/organization-info-first-time-user-edit", name="organization-info-first-time-user-edit", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function organizationInfoFirstTimeUserEdit(Request $request, TokenStorageInterface $tokenStorage,
                                                  MyAccountService $myAccountService,
                                                  TrnVolunterInterestRepository $trnVolunterInterestRepository) : Response{
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $trnBankDetailsArr = $appUser->getTrnBankDetails();
        $trnBankDetail = null;
        if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
            $trnBankDetail = $trnBankDetailsArr[0];
        }
        $arrVolunteerDocuments = $arrAvailability = $arrPrimaryAI = $arrSecondaryAI = array();
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (!empty($objTrnVolunterDetail)) {
            $trnVolunterInterestArr =  $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                if (!empty($trnVolunterInterest->getAreaInterestPrimary())) {
                    $arrPrimaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()] =
                        $trnVolunterInterest->getAreaInterestPrimary();
                    foreach ($trnVolunterInterest->getAreaInterestSecondary() as $secAI) {
                        $arrSecondaryAI[$trnVolunterInterest->getAreaInterestPrimary()->getId()][$secAI->getId()] = $secAI;
                    }
                }
            }
            foreach ($objTrnVolunterDetail->getTrnVolunterAvailabilities() as $trnVolunterAvailability) {
                $arrAvailability[ucfirst($trnVolunterAvailability->getAvailableOnTime())
                ][$trnVolunterAvailability->getAvailableOnDay()] = strtoupper(substr($trnVolunterAvailability->getAvailableOnDay(), 0, 3))  ;
            }
        }
        $arrVolunteerDocuments = array();
        foreach ($appUser->getTrnOrganizationUploadDocuments() as $trnVolunteerDocument) {
            $arrVolunteerDocuments[] = $trnVolunteerDocument->getuploadDocumentPath();
        }
        return $this->render('portal/my-account/organization/organization-info-first-time-user-edit.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'trnVolunterDetail' =>
                $objTrnVolunterDetail, 'appUserInfo' => $appUser->getAppUserInfo(), 'trnBankDetails' => $trnBankDetail,
            'arrPrimaryAI' => $arrPrimaryAI, 'arrSecondaryAI' => $arrSecondaryAI, 'arrAvailability' => $arrAvailability,
            'arrVolunteerDocuments' => $arrVolunteerDocuments
        ]);
    }
    #############################################   Organization Information ######################################

    #############################################   Social Profile ######################################

    /**
     * @Route("/my-account/social-profile", name="social-profile", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function socialProfile(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $this->arrSocialProfileData = $myAccountService->getSocialProfileData($appUser);
        return $this->render('portal/my-account/social-profile/social-profile.html.twig', [
            'appUser'=> $appUser, 'arrSocialProfileData' => $this->arrSocialProfileData
        ]);
    }

    /**
     * @Route("/my-account/social-profile-empty-info", name="social-profile-empty-info", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function socialProfileEmptyInfo(Request $request) :Response
    {
        return $this->render('portal/my-account/social-profile/social-profile-empty.html.twig', [
        ]);
    }

    /**
     * @Route("/my-account/social-profile-activated", name="social-profile-activated", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function socialProfileActivated(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository) :Response
    {
        $arrDonationData = $this->arrSocialProfileData['arrDonationData'];
        $arrEvents = $arrEventUpComingOrOnGoingDetails = array();
        $arrDonationRecurringData = $arrDonationPrimarySec = array();
        foreach ($arrDonationData as $key => $donationData) {
            if (!empty($donationData->getTrnCircleEvent()))
                $arrEvents[] = $donationData->getTrnCircleEvent();
        }
        foreach ($arrDonationData as $key => $donationData) {
            if (!empty($donationData->getTrnCircleEvent())) {
                $arrTrnAreaOfInterests = $donationData->getTrnCircleEvent()->getTrnAreaOfInterests();
                foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
                    $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
                    $arrTemp = array();
                    foreach ($areaInterestSecondary as $areaInterest) {
                        $arrTemp[] = $areaInterest->getAreaInterest();
                    }
                    $arrDonationPrimarySec[$donationData->getId()][] = $TrnAreaOfInterest->getAreaInterestPrimary()
                            ->getAreaInterest() . ' - ' . implode($arrTemp);
                }
                $TrnCircleEvent = $donationData->getTrnCircleEvent();
                $trnFundRaiserCircleEventDetails = $TrnCircleEvent->getTrnFundRaiserCircleEventDetails();
                foreach ($trnFundRaiserCircleEventDetails as $trnFundRaiserCircleEventDetail) {
                    $strRecurring = $trnFundRaiserCircleEventDetail->getMstEventOccurrence()->getEventOccurrence();
                    $startDate = $trnFundRaiserCircleEventDetail->getFromDate()->format('d M Y');
                    $endDate = $trnFundRaiserCircleEventDetail->getToDate()->format('d M Y');
                    if ($strRecurring == 'Recurring') {
                        $strRecurring = "OnGoing <br/><em>Validity from $startDate to $endDate</em>";
                    } else {
                        $strRecurring = 'One Time';
                    }
                    $arrDonationRecurringData[$donationData->getId()] = $strRecurring;
                }
            }

        }
        if (!empty($arrEvents)) {
            $entityManager = $this->getDoctrine()->getManager();
            $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEvents, $entityManager);
        }
        return $this->render('portal/my-account/social-profile/social-profile-activated.html.twig', [
            'arrSocialProfileData' => $this->arrSocialProfileData, 'arrDonationPrimarySec' => $arrDonationPrimarySec,
            'arrDonationRecurringData' => $arrDonationRecurringData,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
    }

    #############################################   Social Profile ######################################

    #############################################   Project Info #######################################

    /**
     * @Route("/my-account/project-empty-info", name="project-empty-info", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function projectEmptyInfo(Request $request) :Response
    {
        return $this->render('portal/my-account/project-info/project-empty.html.twig', [
        ]);
    }

    /**
     * @Route("/my-account/projects", name="my-accounts-projects", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function myAccountProjects(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $this->arrSocialProfileData = $myAccountService->getSocialProfileData($appUser);
        return $this->render('portal/my-account/project-info/project-info.html.twig', [
            'appUser'=> $appUser, 'arrSocialProfileData' => $this->arrSocialProfileData
        ]);
    }

    /**
     * @Route("/my-account/project-list", name="my-accounts-project-list", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function projectList(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnProjects = $myAccountService->getOwnProjects();
        $arrTemp = $myAccountService->getProjectsIJoined();
        $arrTempCoCorePrj = $myAccountService->getMyCoCoreProjects();
        $arrProjectsIJoined = $arrRequestToJoinDate = $arrEventsJoined = array();
        $arrMyCoCoreProjects = array();
        foreach ($arrTemp as $data) {
            $arrProjectsIJoined[] = $data[0];
            $arrRequestToJoinDate[$data[0]->getId()] = $data['requestOn'];
            $arrEventsJoined[$data[0]->getId()] = $data['eventJoined'];
        }
        if (!empty($arrTempCoCorePrj)){
            foreach ($arrTempCoCorePrj as $coproject) {
                $arrMyCoCoreProjects[] = $coproject->getTrnCircle();
            }
         }

        return $this->render('portal/my-account/project-info/projects-list.html.twig', [
            'appUser'=> $appUser, 'arrOwnProjects' => $arrOwnProjects, 'arrProjectsIJoined' => $arrProjectsIJoined,
            'arrRequestToJoinDate' => $arrRequestToJoinDate, 'arrMyCoCoreProjects' => $arrMyCoCoreProjects,
            'arrEventsJoined'=> $arrEventsJoined
        ]);
    }

    /**
     * @Route("/my-account/ajax-own-project-list", name="my-accounts-ajax-own-project-list", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function _ajaxOwnProjectList(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnProjects = $myAccountService->getOwnProjects();
        $arrTemp = $myAccountService->getProjectsIJoined();
        $arrTempCoCorePrj = $myAccountService->getMyCoCoreProjects();
        $arrProjectsIJoined = $arrRequestToJoinDate = $arrEventsJoined = array();
        $arrMyCoCoreProjects = array();
        foreach ($arrTemp as $data) {
            $arrProjectsIJoined[] = $data[0];
            $arrRequestToJoinDate[$data[0]->getId()] = $data['requestOn'];
            $arrEventsJoined[$data[0]->getId()] = $data['eventJoined'];
        }
        if (!empty($arrTempCoCorePrj)){
            foreach ($arrTempCoCorePrj as $coproject) {
                $arrMyCoCoreProjects[] = $coproject->getTrnCircle();
            }
        }
        return $this->render('portal/my-account/project-info/_ajax-own-project-list.html.twig', [
            'appUser'=> $appUser, 'arrOwnProjects' => $arrOwnProjects, 'arrProjectsIJoined' => $arrProjectsIJoined,
            'arrRequestToJoinDate' => $arrRequestToJoinDate, 'arrMyCoCoreProjects' => $arrMyCoCoreProjects,
            'arrEventsJoined'=> $arrEventsJoined
        ]);
    }

    /**
     * @Route("/my-account/ajax-other-project-list", name="my-accounts-ajax-other-project-list", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function _ajaxOtherProjectList(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrTemp = $myAccountService->getProjectsIJoined();
        $arrProjectsIJoined = $arrRequestToJoinDate = $arrEventsJoined = array();
        foreach ($arrTemp as $data) {
            $arrProjectsIJoined[] = $data[0];
            $arrRequestToJoinDate[$data[0]->getId()] = $data['requestOn'];
            $arrEventsJoined[$data[0]->getId()] = $data['eventJoined'];
        }
        return $this->render('portal/my-account/project-info/_ajax-other-project-list.html.twig', [
            'appUser'=> $appUser, 'arrProjectsIJoined' => $arrProjectsIJoined,
            'arrRequestToJoinDate' => $arrRequestToJoinDate, 'arrEventsJoined' => $arrEventsJoined
        ]);
    }

    /**
     * @Route("/my-account/project-deactivate-popup/{id}", name="my-accounts-project-deactivate-popup", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @return Response
     */
    public function projectDeactivatePopUp(Request $request, TrnCircle $trnCircle) :Response
    {
        return $this->render('portal/my-account/project-info/_ajax-project-deactivate-popup.html.twig', [ 'circle' =>
            $trnCircle ]);
    }

    /**
     * @Route("/my-account/project-deactivate/{id}", name="my-accounts-project-deactivate", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @return JsonResponse
     */
    public function projectDeactivate(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService) :JsonResponse{
        $myAccountService->deactivateProject($trnCircle);
        return new JsonResponse([ 'Message' => 'Successfully Deactivated project']);
    }

    /**
     * @Route("/my-account/exit-project/{id}", name="my-accounts-exit-project", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @return JsonResponse
     */
    public function exitProject(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService) :JsonResponse {
        $myAccountService->exitProject($trnCircle);
        return new JsonResponse([ 'Message' => 'Successfully exited from the project']);
    }

    /**
     * @Route("/my-account/project-exit-popup/{id}", name="my-accounts-project-exit-popup", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function exitProjectPopUp(Request $request, TrnCircle $trnCircle, TokenStorageInterface $tokenStorage,
                                     MyAccountService $myAccountService) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $isUserAddressUsedAsCollectionCenter = $myAccountService->isUserAddressUsedAsCollectionCenter($appUser,
        $trnCircle);
        if(empty($isUserAddressUsedAsCollectionCenter)){
            return $this->render('portal/my-account/project-info/_ajax-exit-project-popup.html.twig', ['circle' =>
                $trnCircle]);
        } else {
            return $this->render('portal/my-account/project-info/_ajax-cannot-exit-project-popup.html.twig', ['circle' =>
                $trnCircle]);
        }
    }

    /**
     * @Route("/my-account/view-edit-project/{id}", name="my-accounts-view-edit-project", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param SessionInterface $session
     * @return Response
     */
    public function viewEditProject(Request $request, TrnCircle $trnCircle, SessionInterface $session) :Response {
        $arrTrnAreaOfInterests = $trnCircle->getTrnAreaOfInterests();
        $arrPrimaryAISecAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getId();
            }
        }
        $session->set('areaOfInterest', $arrPrimaryAISecAI);
        $session->set('create-project',true);
        $session->set('trnCircleId', $trnCircle->getId());
        return $this->redirectToRoute('create-your-own-project-review');
    }

    /**
     * @Route("/my-account/view-project-members/{id}", name="my-accounts-view-project-members", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleRepository $trnCircleRepository
     * @return Response
     */
    public function viewProjectMembers(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService,
                                       TokenStorageInterface $tokenStorage, TrnCircleRepository $trnCircleRepository) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnProjects = $trnCircleRepository->findBy(['id' => $trnCircle->getId() ]);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircle);
        return $this->render('portal/my-account/project-info/view-project-member.html.twig', [ 'circle' =>
            $trnCircle, 'arrProjectMemberListData' => $arrProjectMemberListData, 'appUser'=> $appUser,
            'arrOwnProjects'=> $arrOwnProjects ]);
    }

    /**
     * @Route("/my-account/ajax-view-project-members/{id}", name="my-accounts-ajax-view-project-members", methods={"POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function ajaxViewProjectMembers(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService,
                                       TokenStorageInterface $tokenStorage, TrnCircleRepository $trnCircleRepository) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnProjects = $trnCircleRepository->findBy(['id' => $trnCircle->getId() ]);
        $arrParameters = array();
        $arrParameters['quicksearch'] = $request->get('quicksearch');
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircle, $arrParameters);
        return $this->render('portal/my-account/project-info/_ajax-view-project-member.html.twig', [ 'circle' =>
            $trnCircle, 'arrProjectMemberListData' => $arrProjectMemberListData, 'appUser'=> $appUser,
            'arrOwnProjects'=> $arrOwnProjects ]);
    }

    /**
     * @Route("/my-account/view-project-request-to-join/{id}", name="view-project-request-to-join", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @param MstStatusRepository $mstStatusRepository
     * @return Response
     */
    public function viewProjectRequestToJoin(Request $request, TrnCircle $trnCircle, MyAccountService
    $myAccountService, TokenStorageInterface $tokenStorage, MstEventProductTypeRepository
    $mstEventProductTypeRepository, MstStatusRepository $mstStatusRepository) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $projectRequestToJoinMemberListData = $myAccountService->getProjectRequestToJoinMemberList($trnCircle);
        $arrMstEventProductTypeObj = $mstEventProductTypeRepository->findBy(["isActive" => true]);
        $arrMstStatus = $mstStatusRepository->findBy(["status" => array('Rejected', 'Pending Activation')]);
        $arrTrnCircleEvents = $trnCircle->getTrnCircleEvents();
        return $this->render('portal/my-account/project-info/view-project-request-to-join.html.twig', ['circle' =>
            $trnCircle, 'projectRequestToJoinMemberListData' => $projectRequestToJoinMemberListData, 'appUser' =>
            $appUser, 'arrMstEventProductTypeObj' => $arrMstEventProductTypeObj, 'arrMstStatus' => $arrMstStatus,
            'arrTrnCircleEvents' => $arrTrnCircleEvents]);
    }

    /**
     * @Route("/my-account/view-project-event-participation/{id}", name="view-project-event-participation", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @param MstStatusRepository $mstStatusRepository
     * @return Response
     */
    public function viewProjectEventParticipation(Request $request, TrnCircle $trnCircle, MyAccountService
    $myAccountService, TokenStorageInterface $tokenStorage, MstEventProductTypeRepository
    $mstEventProductTypeRepository, MstStatusRepository $mstStatusRepository) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrProjectEventParticipationIndividualData = $myAccountService->getProjectEventParticipationIndividualData
        ($trnCircle, $appUser);
        return $this->render('portal/my-account/project-info/view-project-event-participation-new.html.twig', ['circle' =>
            $trnCircle, 'arrProjectEventParticipationIndividualData' => $arrProjectEventParticipationIndividualData, 'appUser' => $appUser,
        ]);
    }

    /**
     * @Route("/my-account/ajax-view-project-event-participation/{id}", name="ajax-view-project-event-participation", methods={"POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function ajaxViewProjectEventParticipation(Request $request, TrnCircle $trnCircle, MyAccountService
    $myAccountService, TokenStorageInterface $tokenStorage) :Response
    {
        $fromDate = $request->get('from');
        $toDate = $request->get('to');
        $quicksearch = $request->get('quicksearch');
        $arrParameters = array();
        if ($fromDate) {
            $arrParameters['from'] = $fromDate;
        }
        if ($toDate) {
            $arrParameters['to'] = $toDate;
        }
        if ($quicksearch) {
            $arrParameters['quicksearch'] = $quicksearch;
        }
        $appUser = $tokenStorage->getToken()->getUser();
        $arrProjectEventParticipationIndividualData = $myAccountService->getProjectEventParticipationIndividualData
        ($trnCircle, $appUser, $arrParameters);
        return $this->render('portal/my-account/project-info/_ajax-view-project-event-participation-new.html.twig', ['circle' =>
            $trnCircle, 'arrProjectEventParticipationIndividualData' => $arrProjectEventParticipationIndividualData, 'appUser' => $appUser,
        ]);
    }

    /**
     * @Route("/my-account/project-view-lead/{id}", name="project-view-lead", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function projectViewLead(Request $request, TrnCircle $trnCircle, TokenStorageInterface $tokenStorage,
                                    MyAccountService $myAccountService) :Response{
        $appUser = $tokenStorage->getToken()->getUser();
        $projectEventLeadData = $myAccountService->getProjectEventLeadData($trnCircle);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircle);
        return $this->render('portal/my-account/project-info/project-view-lead.html.twig', [ 'circle' =>
            $trnCircle, 'projectEventLeadData' => $projectEventLeadData, 'appUser' => $appUser ,
            'arrProjectMemberListData' => $arrProjectMemberListData ]);
    }

    /**
     * @Route("/my-account/get-project-member-list/{id}", name="my-account-get-project-member-list", methods={"POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function getProjectMemberList(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService)
    :Response {
        $arrParameters = array();
        $arrParameters['quicksearch'] = $request->get('quicksearch');
        $projectEventLeadData = $myAccountService->getProjectEventLeadData($trnCircle);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircle, $arrParameters);
        return $this->render('portal/my-account/project-info/_ajax-project-view-lead.html.twig', [ 'circle' =>
            $trnCircle, 'arrProjectMemberListData' => $arrProjectMemberListData, 'projectEventLeadData' => $projectEventLeadData ]);
    }

    /**
     * @Route("/my-account/project-broadcast/{id}", name="my-account-project-broadcast", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param FileUploaderHelper $fileUploaderHelper
     * @param AppUserRepository $appUserRepository
     * @return Response
     * @throws \Exception
     */
    public function projectBroadcast(Request $request, TrnCircle $trnCircle, MyAccountService $myAccountService,
                                     TokenStorageInterface $tokenStorage, FileUploaderHelper $fileUploaderHelper,
                                     AppUserRepository $appUserRepository)  :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $projectBroadCastMessagesData = $myAccountService->getProjectBroadCastMessages($trnCircle);
        $arrProjectMemberList = $myAccountService->getProjectMemberList($trnCircle);
        $arrContributorData = $arrProjectMemberList['arrContributorData'];
        if($request->isMethod('POST')) {
            $textBroadCastMessage = $request->get('message');
            $hdnSentTo = $request->get('hdnSentTo');
            $hdnBroadCastMembers = $request->get('hdnBroadCastMembers');
            $arrAppUsers = array();
            if($hdnSentTo == 'All') {
                if (!empty($arrContributorData)) {
                    foreach ($arrContributorData as $objTrnCircleRequestToJoin ) {
                        $arrAppUsers[] = $objTrnCircleRequestToJoin->getAppUser();
                    }
                }
            } else {
                $checkBoxNameToSubmit = explode(',', $hdnBroadCastMembers);
                $arrAppUsers = $appUserRepository->findBy([ 'id' => $checkBoxNameToSubmit]);
            }
            $filename = $request->files->get('filename');
            $newFilename = "";
            $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
            if (!empty($filename)){
                $newFilename = $fileUploaderHelper->uploadPublicFile($filename, 'Document'.Uuid::uuid4()->toString(), null);
            }
            $myAccountService->projectBroadcastUpdate($trnCircle, $arrAppUsers,  $textBroadCastMessage, $hdnSentTo, $newFilename,
                $orgCompany);
            $this->addFlash('success', 'Broadcast update successfully sent.');
            return $this->redirectToRoute('my-accounts-projects');
        }
        return $this->render('portal/my-account/project-info/project-broadcast.html.twig', [ 'circle' =>
            $trnCircle, 'projectBroadCastMessagesData' => $projectBroadCastMessagesData, 'appUser' => $appUser,
            'arrContributorData' => $arrContributorData ]);
    }

    /**
     * @Route("/my-account/update-project-participation-request", name="update-project-participation-request", methods={"POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @param Mailer $mailer
     * @return JsonResponse
     */
    public function updateProjectParticipationRequest(Request $request, MyAccountService $myAccountService, Mailer $mailer)  :JsonResponse
    {
        $requestId = $request->get('requestid');
        $status = $request->get('status');
        $arrResponse = $myAccountService->updateProjectParticipation($requestId, $status);
        return new JsonResponse($arrResponse);
    }

    /**
     * @Route("/my-account/update-project-event-participation-request", name="update-project-event-participation-request",
     *     methods={"POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @param Mailer $mailer
     * @return JsonResponse
     */
    public function updateProjectEventParticipationRequest(Request $request, MyAccountService $myAccountService, Mailer $mailer)  :JsonResponse
    {
        $requestId = $request->get('requestid');
        $status = $request->get('status');
        $arrResponse = $myAccountService->updateProjectEventParticipation($requestId, $status);
        return new JsonResponse($arrResponse);
    }

    /**
     * @Route("/my-account/ajax-view-project-request-to-join/{id}", name="ajax-view-project-request-to-join", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param MyAccountService $myAccountService
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function ajaxViewProjectRequestToJoin(Request $request, TrnCircle $trnCircle, MyAccountService
    $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository) :Response {
        $arrParameters = array();
        $quicksearch = $request->get('quicksearch');
        $Events      = $request->get('Events');
        $Resources   = $request->get('Resources');
        $Status      = $request->get('Status');
        if (!empty($quicksearch)){
            $arrParameters['quicksearch'] = $quicksearch;
        }
        if (!empty($Events)){
            $arrParameters['Events'] = $trnCircleEventsRepository->find($Events);
        }
        if (!empty($Resources)){
            $arrParameters['mstProductType'] = $Resources;
        }
        if (!empty($Status)){
            $arrParameters['mstStatus'] = $Status;
        }
        $projectRequestToJoinMemberListData = $myAccountService->getProjectRequestToJoinMemberList($trnCircle, $arrParameters);
        return $this->render('portal/my-account/project-info/_ajax-view-project-request-to-join.html.twig', [
            'projectRequestToJoinMemberListData' => $projectRequestToJoinMemberListData]);
    }

    /**
     * @Route("/my-account/add-remove-project-lead", name="add-remove-project-lead", methods={"POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @return JsonResponse
     */
    public function addRemoveProjectLead(Request $request, MyAccountService $myAccountService) :JsonResponse
    {
        $projectId = $request->get('projectid');
        $appUserId = $request->get('appUserId');
        $eventId   = $request->get('eventId');
        if (empty($eventId))
            $eventId = 0;
        $action   = $request->get('action');
        $myAccountService->addRemoveProjectLead($projectId, $appUserId, $eventId, $action);
        if ($action == 'add')
            return new JsonResponse([ 'Message' => 'Successfully added member as lead']);
        else
            return new JsonResponse([ 'Message' => 'Successfully removed member as lead']);
    }

    /**
     * @Route("/my-account/invite-to-join/{id}", name="my-account-invite-to-join", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircle $trnCircle
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function inviteToJoin(Request $request, TrnCircle $trnCircle, TokenStorageInterface $tokenStorage,
                                 MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrUserContacts = $myAccountService->getUserContacts($appUser);
        $arrUserContactsAlreadySent = $myAccountService->checkIfInviteAlreadySent($arrUserContacts, $trnCircle);
        if($request->isMethod('POST')) {
            $checkBoxNameToSubmit = $request->get('checkBoxNameToSubmit');
            $projectLink = $this->generateUrl('project-details', array('id' => $trnCircle->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL);
            $myAccountService->sendInviteToJoin($checkBoxNameToSubmit, $trnCircle, $projectLink);
            $this->addFlash('success', 'Invite Successfully sent to selected contacts.');
            return $this->redirectToRoute('my-accounts-projects');
        }
        return $this->render('portal/my-account/project-info/invite-to-join.html.twig', [
            'arrUserContacts' => $arrUserContacts,  'appUser' => $appUser, 'circle' => $trnCircle,
            'arrUserContactsAlreadySent' => $arrUserContactsAlreadySent]);
    }

    #############################################   Project Info ######################################

    ########################################## Contact Book #############################################
    /**
     * @Route("/my-account/contact-book", name="contact-book", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function contactBook(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrUserContacts = $myAccountService->getUserContacts($appUser);
        if (empty($arrUserContacts)){
            return $this->render('portal/my-account/contact-book/contact-book-empty.html.twig', [
                'appUser'=> $appUser
            ]);
        }
        return $this->render('portal/my-account/contact-book/contact-book-info.html.twig', [
            'appUser'=> $appUser, 'arrUserContacts' => $arrUserContacts
        ]);
    }

    /**
     * @Route("/my-account/create-contact-book", name="my-account-create-contact-book", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnAppUserContactsRepository $trnAppUserContactsRepository
     * @param MstSalutationRepository $mstSalutationRepository
     * @return Response
     */
    public function createContactBook(Request $request, TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, TrnAppUserContactsRepository $trnAppUserContactsRepository, MstSalutationRepository $mstSalutationRepository)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        if($request->isMethod('POST')) {
            $title	= $request->get('title');
            $firstName	= $request->get('firstName');
            $lastName	= $request->get('lastName');
            $countryCode= $request->get('countryCode');
            $mobileNo	= $request->get('mobileNo');
            $emailId	= $request->get('emailId');

            $trnContact = $trnAppUserContactsRepository->findOneBy(['email' => $emailId, 'isActive' => 1,
                'appUser' => $appUser]);
            if(!empty($trnContact)) {
                $this->addFlash('error', 'User with email already exist in your contact book');
            } else {
                $myAccountService->addCreateContactBook($title, $firstName, $lastName, $countryCode, $mobileNo,
                    $emailId);
                $this->addFlash('success', 'Successfully added contact.');
            }
            return $this->redirectToRoute('contact-book');
        }
        $arrMstSalutation = $mstSalutationRepository->findAll();
        return $this->render('portal/my-account/contact-book/create-contact.html.twig', [
            'appUser'=> $appUser, 'arrMstSalutation' => $arrMstSalutation
        ]);
    }

    /**
     * @Route("/my-account/edit-contact/{id}", name="my-account-edit-contact", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnAppUserContacts $trnAppUserContacts
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param MstSalutationRepository $mstSalutationRepository
     * @return Response
     */
    public function editContact(Request $request, TrnAppUserContacts $trnAppUserContacts, TokenStorageInterface
    $tokenStorage, MyAccountService $myAccountService, MstSalutationRepository $mstSalutationRepository) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        if($request->isMethod('POST')) {
            $title	= $request->get('title');
            $firstName	= $request->get('firstName');
            $lastName	= $request->get('lastName');
            $countryCode= $request->get('countryCode');
            $mobileNo	= $request->get('mobileNo');
            $emailId	= $request->get('emailId');
            $myAccountService->editCreateContactBook($title, $firstName, $lastName, $countryCode, $mobileNo, $emailId,
                $trnAppUserContacts);
            $this->addFlash('success', 'Successfully edited contact.');
            return $this->redirectToRoute('contact-book');
        }
        $arrMstSalutation = $mstSalutationRepository->findAll();
        return $this->render('portal/my-account/contact-book/edit-contact.html.twig', [
            'appUser'=> $appUser, 'trnAppUserContacts' => $trnAppUserContacts, 'arrMstSalutation' => $arrMstSalutation
        ]);
    }

    /**
     * @Route("/my-account/delete-contact", name="my-account-delete-contact", methods={"GET", "POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteContact(Request $request, MyAccountService $myAccountService) :JsonResponse
    {
        if ($request->isMethod('POST')) {
            $contactId = $request->get('contactid');
            $myAccountService->deactivateContact($contactId);
            return new JsonResponse([ 'Message' => 'Successfully deleted the contact', 'status' => 1]);
        }
        return new JsonResponse([ 'Message' => 'Not available', 'status' => 0]);
    }

    /**
     * @Route("/my-account/ajax-contact-book", name="my-account-ajax-contact-book", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function _ajaxContactBook(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $searchContact = $request->get('searchContact');
        $arrUserContacts = $myAccountService->getUserContacts($appUser, $searchContact);
        return $this->render('portal/my-account/contact-book/_ajax-contact-book-info.html.twig', [
            'appUser'=> $appUser, 'arrUserContacts' => $arrUserContacts
        ]);
    }

    /**
     * @Route("/my-account/import-contact", name="my-account-import-contact", methods={"GET", "POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function importContact(Request $request, MyAccountService $myAccountService) :Response {
        if ($request->isMethod('POST')) {
            $UploadedFile = $request->files->get('filename');
            $arrReturnData = $myAccountService->importContact($UploadedFile);
            $error = $myAccountService->errorWhileUpload();
            if ($error) {
                $this->addFlash('error', implode(', ', $arrReturnData));
            } else {
                $this->addFlash('success', 'Successfully imported contact.');
            }
            return $this->redirectToRoute('contact-book');
        }
    }

    ########################################## Reports #############################################

    /**
     * @Route("/my-account/personal-info-org", name="personal-info-org", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function personalInfoOrg(Request $request, TokenStorageInterface $tokenStorage) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        return $this->render('portal/my-account/social-profile.html.twig', [
            'appUser'=> $appUser
        ]);
    }

    ########################################## Reports #############################################

    /**
     * @Route("/my-account/reports", name="my-account-reports", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function myAccountReports(Request $request, TokenStorageInterface $tokenStorage) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        return $this->render('portal/my-account/reports/reports.html.twig', [
            'appUser'=> $appUser
        ]);
    }

    ########################################## Reports #############################################
    ########################################## Settings #############################################

    /**
     * @Route("/my-account/settings", name="my-account-settings", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function myAccountSettings(Request $request, TokenStorageInterface $tokenStorage) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        return $this->render('portal/my-account/setting/setting.html.twig', [
            'appUser'=> $appUser
        ]);
    }

    /**
     * @Route("/my-account/update-my-password", name="my-account-update-my-password", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param UserPasswordEncoderInterface $encoder
     * @param SessionInterface $session
     * @return Response
     */
    public function updateMyPassword(Request $request, TokenStorageInterface $tokenStorage,
                                     UserPasswordEncoderInterface $encoder, SessionInterface $session) :Response
    {
        if($request->isMethod('POST')) {
            $appUser = $tokenStorage->getToken()->getUser();
            $oldPassword = $request->get('oldPassword');
            $password    = $request->get('password');
            $newConfirmPassword = $request->get('newConfirmPassword');
            $isPasswordValid = $encoder->isPasswordValid($appUser, $oldPassword);
            if(!$isPasswordValid) {
                $this->addFlash('error', 'Incorrect Old Password.');
                return $this->render('portal/my-account/setting/setting.html.twig', [
                    'appUser'=> $appUser
                ]);
            }
            if($password != $newConfirmPassword) {
                $this->addFlash('error', 'Password and confirm password mismatch.');
                return $this->render('portal/my-account/setting/setting.html.twig', [
                    'appUser'=> $appUser
                ]);
            }
            $password = $encoder->encodePassword($appUser, $password);
            $appUser->setUserPassword($password);
            $appUser->setUserResetPasswordToken(null);
            $appUser->setUserResetPasswordTokenExpiry(null);
            $this->getDoctrine()->getManager()->flush();
            $token = new UsernamePasswordToken($appUser, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            $this->addFlash('requestjoinerror', "Your have successfully reset your password and you are now redirected to login page.");
            return $this->redirectToRoute('personal-info');
        }
        return $this->redirectToRoute('personal-info');
    }

    /**
     * @Route("/my-account/subscribe-unsubscribe-newletter", name="my-account-subscribe-unsubscribe-newletter", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param Mailer $mailer
     * @param NotificationService $notificationService
     * @return Response
     */
    public function subscribeUnSubscribeNewsLetter(Request $request, TokenStorageInterface $tokenStorage,
                                                   Mailer $mailer, NotificationService $notificationService) :Response {
        if($request->isMethod('POST')) {
            $hdnSubUnSub = $request->get('hdnSubUnSub');
            $appUser = $tokenStorage->getToken()->getUser();
            if($hdnSubUnSub == 'Unsubscribe'){
                $appUser->getAppUserInfo()->setIsSubscribedToNewLetter(0);
                $this->addFlash('success', 'Successfully unsubscribed.');
            } else {
                $notificationService->setAppUser($appUser);
                $notificationService->doProcess('Subscribe');
                $appUser->getAppUserInfo()->setIsSubscribedToNewLetter(1);
                $this->addFlash('success', 'Successfully subscribed.');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUser);
            $entityManager->flush();
            return $this->redirectToRoute('personal-info');
        }
        return $this->redirectToRoute('personal-info');
    }
    ########################################## Settings #############################################

    ######################################## Profile Image###########################################

    /**
     * @Route("/my-account/upload-profile-pic", name="my-account-upload-profile-pic", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws \Exception
     */
    public function uploadProfilePic(Request $request, TokenStorageInterface $tokenStorage,
                                     FileUploaderHelper $fileUploaderHelper) :Response {
        if ($request->isMethod('POST')) {
            $hdnPath = $request->get('hdnPath');
            $hdnRoute = $request->get('hdnRoute');
            $arrPath = explode('/', $hdnPath);
            $id = null;
            if (!empty($arrPath) && count($arrPath) > 1) {
                $id =  $arrPath[count($arrPath) - 1];
                if (is_numeric($id) === false) {
                    $id = null;
                }
            }
            $appUser = $tokenStorage->getToken()->getUser();
            $profilePic = $request->files->get('profilePic');
            $newFilename = $fileUploaderHelper->uploadPrivateFile($profilePic, $avatarImage = null);
            $appUser->getAppUserInfo()->setUserAvatarImage($newFilename);
            $appUser->getAppUserInfo()->setUserAvatarImagePath($this->getParameter('user_file_folder'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUser);
            $entityManager->flush();
            if(!empty($id)) {
                return $this->redirectToRoute($hdnRoute, array('id' => $id));
            } else {
                return $this->redirectToRoute($hdnRoute);
            }
        }
        return $this->redirectToRoute('personal-info');
    }

    /**
     * @Route("/my-account/upload-background-pic", name="my-account-upload-background-pic", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws \Exception
     */
    public function uploadBackgroundPic(Request $request, TokenStorageInterface $tokenStorage,
                                        FileUploaderHelper $fileUploaderHelper) :Response {
        if ($request->isMethod('POST')) {
            $hdnPath = $request->get('hdnPath');
            $hdnRoute = $request->get('hdnRoute');
            $arrPath = explode('/', $hdnPath);
            $id = null;
            if (!empty($arrPath) && count($arrPath) > 1) {
                $id =  $arrPath[count($arrPath) - 1];
                if (is_numeric($id) === false) {
                    $id = null;
                }
            }
            $appUser = $tokenStorage->getToken()->getUser();
            $profilePic = $request->files->get('backgroundPic');
            $newFilename = $fileUploaderHelper->uploadPrivateFile($profilePic, $avatarImage = null);
            $appUser->getAppUserInfo()->setBackgroundProfile($newFilename);
            $appUser->getAppUserInfo()->setBackgroundProfilePath($this->getParameter('user_file_folder'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUser);
            $entityManager->flush();
            if(!empty($id)) {
                return $this->redirectToRoute($hdnRoute, array('id' => $id));
            } else {
                return $this->redirectToRoute($hdnRoute);
            }
        }
        return $this->redirectToRoute('personal-info');
    }
    ######################################## Profile Image###########################################
    
}