<?php

namespace App\Controller\Portal;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
use App\Entity\Cms\CmsFaq;
use App\Entity\Cms\CmsPage;
use App\Entity\Form\FormReport;
use App\Entity\Form\FormSupport;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstHighlights;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnProductMedia;
use App\Form\Cms\CmsUserCommentType;
use App\Form\Form\FormReportOtherType;
use App\Form\Form\FormReportSelfType;
use App\Form\Form\FormSupportChangeMakerType;
use App\Form\Form\FormSupportVolunteerType;
use App\Form\Transaction\TrnCirclePortalType;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnProductMediaRepository;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use App\Service\ChangeMakerFilter;
use App\Service\FileUploaderHelper;
use App\Service\Mailer;
use App\Service\MyAccountService;
use App\Service\ProjectService;
use DateTime;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\CommonHelper;
use App\Service\NotificationService;
use Ramsey\Uuid\Uuid;
use function MongoDB\Driver\Monitoring\removeSubscriber;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ProjectController
 * @IsGranted("ROLE_APP_USER")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/create-project", name="create-project", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function createProject(Request $request, SessionInterface $session, TokenStorageInterface $tokenStorage,
                                  TrnVolunterInterestRepository $trnVolunterInterestRepository,
                                  MyAccountService $myAccountService): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();

        if($myAccountService->checkIfMandatoryFieldsFilled($appUser) == false) {
            $this->addFlash('error', 'Please update the Personal Info mandatory fields in order to proceed.');
            return $this->redirectToRoute('personal-info');
        }

        $areaInterests = array();
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (!empty($objTrnVolunterDetail)) {
            $trnVolunterInterestArr = $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                $areaInterests[] = $trnVolunterInterest->getAreaInterestPrimary();
            }
        }
        if($request->isMethod('POST')){
            return $this->redirectToRoute('create-own-project-details');
        }
        $session->remove('areaOfInterest');
        $session->remove('trnCircleId');
        return $this->render('portal/project-details/create-your-own-project.html.twig',
            [ 'areaInterests' => $areaInterests, 'selectedAreaOfInterest' =>  array()]);
    }

    /**
     * @Route("/edit-project-of-interest", name="edit-project-of-interest", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function editProjectAreaOfInterest(Request $request, SessionInterface $session, TokenStorageInterface
    $tokenStorage,  TrnVolunterInterestRepository $trnVolunterInterestRepository):
    Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $areaInterests = array();
        $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
        if (!empty($objTrnVolunterDetail)) {
            $trnVolunterInterestArr = $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
            foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                $areaInterests[] = $trnVolunterInterest->getAreaInterestPrimary();
            }
        }

        $arrAreaOfInterest = $session->get('areaOfInterest', array());
        $arrSecondaryAIData = array();
        foreach ($arrAreaOfInterest as $nPrimaryAI => $arrSecondaryAI) {
            foreach ($arrSecondaryAI as  $key => $nSecAI) {
                $arrSecondaryAIData[$nPrimaryAI][] = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find
                ($nSecAI)->getAreaInterest();
            }
        }
                
        if($request->isMethod('POST')){
            return $this->redirectToRoute('create-own-project-details');
        }
        return $this->render('portal/project-details/create-your-own-project.html.twig',
            [ 'areaInterests' => $areaInterests, 'selectedAreaOfInterest' => $arrAreaOfInterest, 'arrSecondaryAIData'
            => $arrSecondaryAIData]);
    }

    /**
     * @Route("/create-own-project-details", name="create-own-project-details", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param UserInterface $user
     * @param AppUserRepository $appUserRepository
     * @return Response
     */
    public function createOwnProjectDetails(Request $request, SessionInterface $session, UserInterface $user, AppUserRepository $appUserRepository): Response
    {
        $session->set('create-project',true);
        $arrAreaOfInterest = $session->get('areaOfInterest', array());

        $trnCircleId = $session->get('trnCircleId', null);
        $userId = $user->getId();
        $appUser = $appUserRepository->find($userId);
        if (empty($trnCircleId)){
            $trnCircle = new TrnCircle();
            $trnCircle->setCircle($appUser->getAppUserInfo()->getName());
            $trnCircle->setMstCountry($appUser->getAppUserInfo()->getMstCountry());
            $trnCircle->setMstState($appUser->getAppUserInfo()->getMstState());
            $trnCircle->setMstCity($appUser->getAppUserInfo()->getMstCity());

            if($appUser->getAppUserInfo()->getMstUserMemberType()->getUserMemberType() == 'Individual') {
                // user is Individual
                $tmpObj = $this->getDoctrine()->getRepository(MstHighlights::class)->findOneBy(['highlight' => 'Individual']);
                $trnCircle->setMstHighlights($tmpObj);
            } else {
                // user is Organization
                $tmpObj = $this->getDoctrine()->getRepository(MstHighlights::class)->findOneBy(['highlight' => 'NGO']);
                $trnCircle->setMstHighlights($tmpObj);
            }
            //$trnCircle->setMstHighlights
        }
        else
            $trnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);
        $objAppUserInfo = $appUser->getAppUserInfo();
        $form = $this->createForm(TrnCirclePortalType::class, $trnCircle);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {

            $existitngAIs = array();
            // remove old area of interest
            foreach($trnCircle->getTrnAreaOfInterests() as $existingareas) {
                $existitngAIs[] = $existingareas;
            }

            foreach ($existitngAIs as $aiIds) {
                // Remove old media urls if any
                $this->getDoctrine()->getManager()->remove($aiIds);
                $this->getDoctrine()->getManager()->flush();
            }

            foreach ($arrAreaOfInterest as $nPrimaryAI => $arrSecondaryAI) {
                $objTrnAreaOfInterest = new TrnAreaOfInterest();
                $objTrnAreaOfInterest->setAreaInterestPrimary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nPrimaryAI));
                foreach ($arrSecondaryAI as  $key => $nSecAI) {
                    $objTrnAreaOfInterest->addAreaInterestSecondary($this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI));
                }
                $trnCircle->addTrnAreaOfInterest($objTrnAreaOfInterest);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $trnCircle->setCreatedOn(new \DateTime());
            $trnCircle->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Pending Activation']);
            $trnCircle->setMstStatus($objMstStatus);
            $trnCircle->setAppUser($appUser);
            $trnCircle->setShareCount(0  );
            $trnCircle->setIsActive(0  );
            $trnCircle->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'))  );
            $entityManager->persist($trnCircle);
            $entityManager->flush();
            //$session->remove('areaOfInterest');
            $session->set('trnCircleId', $trnCircle->getId());
            return $this->redirectToRoute('create-your-own-project-gallery');
        } else {
            $trnCircle->setMstCountry($objAppUserInfo->getMstCountry());
            $trnCircle->setMstState($objAppUserInfo->getMstState());
            $trnCircle->setMstCity($objAppUserInfo->getMstCity());
            if(!empty($objAppUser) && !empty($objAppUser->getTrnBankDetails())) {
                $arrTrnBankDetails = $objAppUser->getTrnBankDetails();
                if(!empty($arrTrnBankDetails) && !empty($arrTrnBankDetails[0])) {
                    $objTrnBankDetails = $arrTrnBankDetails[0];
                    $trnCircle->setBeneficiaryBankName($objTrnBankDetails->getBankName());
                    $trnCircle->setBeneficiaryBankAccountNumber($objTrnBankDetails->getAccountNumber());
                    $trnCircle->setBeneficiaryAccountHolderName($objTrnBankDetails->getAccountHolderName());
                    $trnCircle->setBeneficiaryIfscCode($objTrnBankDetails->getIfscCode());
                    $trnCircle->setMstBankAccountTypeBeneficiary($objTrnBankDetails->getMstBankAccountType());
                }
            }
        }
        return $this->render('portal/project-details/create-your-own-project-details.html.twig', [
            'trnCircle' => $trnCircle,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create-your-own-project-gallery", name="create-your-own-project-gallery", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param FileUploaderHelper $fileUploaderHelper
     * @param NotificationService $notificationService
     * @return Response
     * @throws \Exception
     */
    public function createYourOwnProjectGallery(Request $request, SessionInterface $session, FileUploaderHelper
    $fileUploaderHelper, NotificationService $notificationService, ProjectService $projectService): Response
    {

        // move the user to first page of create project as user is back from thank you page
        if(!$session->has('create-project')) {
            return $this->redirectToRoute('create-project');
        }

        $tncContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'terms-and-conditions', 'orgCompany' => $this->getParameter('company_id')]);
        $trnCircleId = $session->get('trnCircleId', null);
        $arrImages = array();
        $arrUrl = array();
        $existingUrls = array();
        $existingGalleryImgs = array();
        if (!empty($trnCircleId)) {
            $trnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);

            if (!empty($trnCircle)) {
                if (!empty($trnCircle->getProfileImagePath()) && $trnCircle->getProfileImagePath() != 'files/') {
                    $arrImages['profile'] = $trnCircle->getProfileImagePath();
                }
                if (!empty($trnCircle->getBackgroundImagePath()) && $trnCircle->getBackgroundImagePath() != 'files/') {
                    $arrImages['backgroundImage'] = $trnCircle->getBackgroundImagePath();
                }
                foreach ($trnCircle->getTrnProductMedia() as $trnProductMedia) {
                    if (  strtolower($trnProductMedia->getMediaType()) == 'image' && $trnProductMedia->getMediaFileName()) {
                        $arrImages['imageGallery'][] =  $trnProductMedia->getuploadedFilePath();
                        $existingGalleryImgs[] = $trnProductMedia;
                    } else if(strtolower($trnProductMedia->getMediaType()) == 'video' && $trnProductMedia->getMediaUrl()) {
                        $arrUrl[] = $trnProductMedia->getMediaUrl();
                        $existingUrls[] = $trnProductMedia;
                    }
                }
            }
        }

        if($request->isMethod('POST')){
            $arrYoutube = $request->get('youtube');
            $trnCircleId = $session->get('trnCircleId', array());
            $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);
            $token = $request->get("token");
            if (!$this->isCsrfTokenValid('upload', $token)) {
                return $this->render('portal/project-details/create-your-own-project-gallery.html.twig', ['error' => 'Invalid Token Please refresh your screen']);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $file = $request->files;
            $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();
            $arrData = array('primaryAreaOfInterest' => '', 'secondaryAreaOfInterest' => array());
            foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
                $arrData['primaryAreaOfInterest'] =  $TrnAreaOfInterest->getAreaInterestPrimary();
                $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
                foreach ($areaInterestSecondary as $areaInterest) {
                    $arrData['secondaryAreaOfInterest'] = $areaInterest;
                    break;
                }
                break;
            }
            if (!empty($file)) {
                $objProfileImageData = $request->files->get('profileImage');
                $objBackGroundImage = $request->files->get('backGroundImage');
                $arrImageGallery = $request->files->get('imageGallery');

                if (!empty($objProfileImageData) && !empty($objProfileImageData[0])) {
                    $objProfileImageData = $objProfileImageData[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objProfileImageData, $objTrnCircle->getCircle().
                        ' profileImage'.Uuid::uuid4()->toString(), null);
                    $objTrnCircle->setProfileImagePath($newFilename);
                }
                if (!empty($objBackGroundImage) && !empty($objBackGroundImage[0])) {
                    $objBackGroundImage = $objBackGroundImage[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objBackGroundImage, $objTrnCircle->getCircle().
                        ' backGroundImage'.Uuid::uuid4()->toString(), null);
                    $objTrnCircle->setBackgroundImagePath($newFilename);
                }

                if (!empty($arrImageGallery)) {

                    // remove old gallery images
                    /*foreach ($existingGalleryImgs as $imgs) {
                        // remove media file from system

                        dd($imgs);

//                        $fileUploaderHelper->removeFile()
                        $this->getDoctrine()->getManager()->remove($imgs);
                    }*/

                    foreach ($arrImageGallery as $key => $objImageGallery) {
                        if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                            $objImageGallery = $objImageGallery[0];
                            $trnProductMedia = new TrnProductMedia();
                            $trnProductMedia->setMstAreaInterestPrimary($arrData['primaryAreaOfInterest']);
                            $trnProductMedia->setMstAreaInterestSecondary($arrData['secondaryAreaOfInterest']);
                            $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $trnProductMedia->setTrnCircle($objTrnCircle);
                            $trnProductMedia->setMediaType('image');
                            $trnProductMedia->setMediaName('GalleryImage'.$objTrnCircle->getId().$key);
                            $trnProductMedia->setMediaAltText('GalleryImage'.$objTrnCircle->getId().$key);
                            $trnProductMedia->setMediaTitle('GalleryImage'.$objTrnCircle->getId().$key);
                            $trnProductMedia->setIsActive(1);
                            $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'GalleryImage'
                                .$objTrnCircle->getId().$key.Uuid::uuid4()->toString(), null);
                            $trnProductMedia->setMediaFileName($newFilename);
                            $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));
                            $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                            $trnProductMedia->setCreatedOn(new DateTime());
                            $entityManager->persist($trnProductMedia);
                        }
                    }
                }
            }
            if (!empty($arrYoutube)) {

                foreach ($existingUrls as $mediaIds) {
                    // Remove old media urls if any
                    $this->getDoctrine()->getManager()->remove($mediaIds);
                }
                foreach ($arrYoutube as $key => $youtubeLink) {
                    $trnProductMedia = new TrnProductMedia();
                    $trnProductMedia->setMstAreaInterestPrimary($arrData['primaryAreaOfInterest']);
                    $trnProductMedia->setMstAreaInterestSecondary($arrData['secondaryAreaOfInterest']);
                    $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnProductMedia->setTrnCircle($objTrnCircle);
                    $trnProductMedia->setIsActive(1);
                    $trnProductMedia->setMediaType('video');
                    $trnProductMedia->setMediaName('YoutubeLink'.$objTrnCircle->getId().$key);
                    $trnProductMedia->setMediaURL($youtubeLink);
                    $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                    $trnProductMedia->setCreatedOn(new DateTime());
                    $entityManager->persist($trnProductMedia);
                }
            }

            //dd($objTrnCircle);

            $entityManager->flush();
            $entityManager->persist($objTrnCircle);
            $entityManager->flush();

            if($request->get('submission_type') == 'review') {
                return $this->redirectToRoute('create-your-own-project-review');
            } else {
                $objTrnCircle->setIsActive(1);
                $entityManager->persist($objTrnCircle);
                $entityManager->flush();
                //Notification to Creator
                $notificationService->setAppUser($objTrnCircle->getAppUser());
                $notificationService->setTrnCircle($objTrnCircle);
                $notificationService->doProcess('Project Created Creator');
                $notificationService->doGCProcess('Project Created GC');
                $projectService->sendNotificationToAllUser($objTrnCircle);

                return $this->redirectToRoute('create-your-own-project-thank-you');
            }
        }
        return $this->render('portal/project-details/create-your-own-project-gallery.html.twig', [
            'tncContent' => $tncContent, 'arrImages' => $arrImages, 'arrUrl' => $arrUrl
        ]);
    }

    /**
     * @Route("/create-your-own-project-review", name="create-your-own-project-review", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @param ProjectService $projectService
     * @return Response
     */
    public function createYourOwnProjectReview(Request $request, SessionInterface $session, NotificationService
    $notificationService, ProjectService $projectService):
    Response
    {
        // move the user to first page of create project as user is back from thank you page
        if(!$session->has('create-project')) {
            return $this->redirectToRoute('create-project');
        }
        $trnCircleId = $session->get('trnCircleId', array());
        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);
        $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();

        $arrPrimaryAI = $arrPrimaryAISecAI = array();

        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            $arrPrimaryAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()] =
                $TrnAreaOfInterest->getAreaInterestPrimary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getAreaInterest();
            }
        }
        if($request->isMethod('POST')){

            $entityManager = $this->getDoctrine()->getManager();

            $objTrnCircle->setIsActive(1);
            $entityManager->persist($objTrnCircle);
            $entityManager->flush();
            //Notification to Creator
            $notificationService->setAppUser($objTrnCircle->getAppUser());
            $notificationService->setTrnCircle($objTrnCircle);
            $notificationService->doProcess('Project Created Creator');
            $notificationService->doGCProcess('Project Created GC');
            //$projectService->sendNotificationToAllUser($objTrnCircle);
            return $this->redirectToRoute('create-your-own-project-thank-you');
        }
        return $this->render('portal/project-details/create-your-own-project-review.html.twig', [ 'arrPrimaryAISecAI'
        => $arrPrimaryAISecAI,  'arrPrimaryAI' => $arrPrimaryAI, 'objTrnCircle' => $objTrnCircle]);
    }

    /**
     * @Route("/create-your-own-project-thank-you", name="create-your-own-project-thank-you", methods={"GET", "POST"})
     * @param Request $request
     * @param UserInterface $user
     * @param AppUserRepository $appUserRepository
     * @param Mailer $mailer
     * @param SessionInterface $session
     * @return Response
     */
    public function createYourOwnProjectThankYou(Request $request, UserInterface $user, AppUserRepository $appUserRepository,SessionInterface $session, Mailer $mailer): Response
    {
        if($session->has('create-project')) {

            $session->remove('create-project');

            // send notification to GC User for project creation
            $userId = $user->getId();
            $appUser = $appUserRepository->find($userId);
            $userEmail = $appUser->getAppUserInfo()->getUserEmail();

            $userFName = $appUser->getAppUserInfo()->getUserFirstName();
            $userLName = $appUser->getAppUserInfo()->getUserLastName();
            $userName = '';


            if($userFName != '') {
                $userName.= ' '.$userFName;
            }
            if($userLName != '') {
                $userName.= ' '.$userLName;
            }
            // if first name and last name both empty, use username to dispaly
            if(trim($userName) == '') {
                $userName = $appUser->getUserName();
            }

            $trnCircleId = $session->get('trnCircleId', array());
            $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);
            $projectName = $objTrnCircle->getCircle();
/*
            $mailer->sendSuccessfulProjectCreationUserMail($userEmail, $userName, $projectName, 'contact@givingcircle.in');
*/
            //$projectLink = $this->generateUrl('project-details', array('id' => $trnCircleId), UrlGeneratorInterface::ABSOLUTE_URL);
            $projectLink = $this->generateUrl('product_circle_edit', array('id' => $trnCircleId), UrlGeneratorInterface::ABSOLUTE_URL);

            // CONFIRM AND UNCOMMENT THE EMAIL
//            $gcEmail = 'contact@givingcircle.in';
//            $gcEmail = $this->getParameter('gc_email');
            $gcEmail = 'jayashree.kotian@vgmrtechsolutions.com';

            // send notification to GC User for project creation
/*            if(isset($gcEmail)) {
                $mailer->sendSuccessfulProjectCreationGCMail($gcEmail, $userName, $projectName, $projectLink);
            }*/
        }
        $session->remove('trnCircleId');
        return $this->render('portal/project-details/create-your-own-project-thank-you.html.twig', []);
    }

    /**
     * @Route("/get-secondary-area-of-interest", name="get-secondary-area-of-interest", methods={"POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @return Response
     */
    public function getSecondaryAreaOfInterest(Request $request, SessionInterface $session, TokenStorageInterface
    $tokenStorage, TrnVolunterInterestRepository $trnVolunterInterestRepository): Response
    {
        $interestId = $request->get('interestId');
        $fromMyAccount = $request->get('fromMyAccount');
        $objAreaInterest = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($interestId);
        if (!empty($fromMyAccount)) {
            $secondaryAreaInterests = $this->getDoctrine()->getRepository(MstAreaInterest::class)->findBy(['isActive' => 1,
                'mstAreaInterestPrimary' => $objAreaInterest], ['sequenceNo' => 'ASC']);
        } else {
            $appUser = $tokenStorage->getToken()->getUser();
            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
            if (!empty($objTrnVolunterDetail)) {
                $trnVolunterInterestArr = $trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                    $objTrnVolunterDetail, 'isActive' => 1));
                foreach ($trnVolunterInterestArr as $trnVolunterInterest) {
                    if ($trnVolunterInterest->getAreaInterestPrimary()->getId() == $interestId) {
                        foreach ($trnVolunterInterest->getAreaInterestSecondary() as $secAI) {
                            $secondaryAreaInterests[] = $secAI;
                        }
                    }
                }
            }
        }
        $arrTemp = $session->get('areaOfInterest', array());
        $arrSelSecAreaInterest = array();
        if (!empty($arrTemp) && !empty($arrTemp[$interestId])) {
            $arrSelSecAreaInterest = $arrTemp[$interestId];
        }
        return $this->render('portal/project-details/_ajax_secondary_area_interest.html.twig', [
            'areaInterest' => $objAreaInterest,
            'secondaryAreaInterests' => $secondaryAreaInterests,
            'arrSelSecAreaInterest' => $arrSelSecAreaInterest
        ]);
    }

    /**
     * @Route("/save-area-of-interest-to-session", name="save-area-of-interest-to-session", methods={"POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function saveAreaOfInterestToSession(Request $request, SessionInterface $session) :Response
    {
        $primaryAreaOfInterest = 0;
        $addSecondary = false;
        $secAIList = $secondaryAreaOfInterest = array();
        if ($request->isMethod('POST')) {
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $arrAreaOfInterest = $session->get('areaOfInterest', array());
            $arrAreaOfInterest[$primaryAreaOfInterest] = array();
            if (!empty($secondaryAreaOfInterest)){
                foreach ($secondaryAreaOfInterest as $areaOfInterest) {
                    $addSecondary = true;
                    $arrAreaOfInterest[$primaryAreaOfInterest][] = $areaOfInterest;
                    $secondaryAreaInterests = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($areaOfInterest);
                    if (!empty($secondaryAreaInterests))
                        $secAIList[] = $secondaryAreaInterests->getAreaInterest();
                }
            }
            if (empty($addSecondary)) {
                unset($arrAreaOfInterest[$primaryAreaOfInterest]);
            }
            $session->set('areaOfInterest', $arrAreaOfInterest);
        }
        return $this->json(array("success" => true, 'primaryAreaOfInterest' => $primaryAreaOfInterest, 'addSecondary'
        => $addSecondary, "count" => count($arrAreaOfInterest), 'secAIList' => $secAIList));
    }

    /**
     * @param SessionInterface $session
     * @return Response
     */
    public function galleryPopUp(SessionInterface $session) : Response
    {
        $trnCircleId = $session->get('trnCircleId', array());
        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($trnCircleId);
        $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();
        $arrTrnProductMedia = $arrPrimaryAreaOfInterest = $arrPrimaryAISecAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $arrData = array('primaryAreaOfInterest' => '', 'secondaryAreaOfInterest' => array());
            $arrData['primaryAreaOfInterest'] = array ( $TrnAreaOfInterest->getAreaInterestPrimary()->getId() =>
            $TrnAreaOfInterest->getAreaInterestPrimary()
                ->getAreaInterest());
            $arrPrimaryAreaOfInterest[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()] = $TrnAreaOfInterest->getAreaInterestPrimary()
                ->getAreaInterest();
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrData['secondaryAreaOfInterest'] = array( $areaInterest->getId() => $areaInterest->getAreaInterest
                ());
                $arrTempData = $this->getDoctrine()->getRepository(TrnProductMedia::class)->findBy
                (array('mstAreaInterestPrimary' => $TrnAreaOfInterest->getAreaInterestPrimary() , 'mstAreaInterestSecondary' => $areaInterest, 'isActive' => 1));
                if (!empty($arrTempData)){
                    foreach ($arrTempData as $objTempData) {
                        $arrTrnProductMedia[] = $objTempData;
                    }
                }
            }
            $arrPrimaryAISecAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()] = $arrData['secondaryAreaOfInterest'];
        }
        return $this->render('portal/project-details/gallery-popup.html.twig', [ 'areaInterests' =>
            $arrPrimaryAreaOfInterest, 'arrPrimaryAISecAIMap' => $arrPrimaryAISecAI, 'arrTrnProductMedia' => $arrTrnProductMedia]);
    }

    /**
     * @Route("/get-project-area-of-interest", name="get-project-area-of-interest", methods={"POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function getProjectAreaOfInterest(Request $request) :Response
    {
        $nProjectId  = $request->get('nProjectId');
        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($nProjectId);
        $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();
        $arrPrimaryAI = $arrPrimaryAISecAI = array();

        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            $arrPrimaryAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()] =
                $TrnAreaOfInterest->getAreaInterestPrimary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getAreaInterest();
            }
        }
        return $this->render('portal/event/_ajax-event-primary-secondary-ai.html.twig', [ 'arrPrimaryAISecAI'
        => $arrPrimaryAISecAI,  'arrPrimaryAI' => $arrPrimaryAI, 'objTrnCircle' => $objTrnCircle]);
    }

    /**
     * @Route("/get-secondary-area-of-interest-event", name="get-secondary-area-of-interest-event", methods={"POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function getSecondaryAreaOfInterestEvent(Request $request): Response
    {
        $interestId = $request->get('nPrimaryAI');
        $nProjectId  = $request->get('nProjectId');
        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($nProjectId);
        $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();
        $objAreaInterest = null;
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            if ($TrnAreaOfInterest->getAreaInterestPrimary()->getId() == $interestId) {
                $objAreaInterest = $TrnAreaOfInterest->getAreaInterestPrimary();
                $secondaryAreaInterests = $TrnAreaOfInterest->getAreaInterestSecondary();
                break;
            }
        }
        return $this->render('portal/event/_ajax_secondary_area_interest.html.twig', [
            'areaInterest' => $objAreaInterest,
            'secondaryAreaInterests' => $secondaryAreaInterests
        ]);
    }

    /**
     * @Route("/remove_image", name="remove_image", methods={"GET","POST"})
     * @param Request $request
     * FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function removeImage(Request $request, FileUploaderHelper $fileUploaderHelper): Response
    {
        $imagetype = $request->query->get('imagetype');
        $imgsrc = $request->query->get('imgsrc');
        $imagePath = '/images/files/';

        $imageName = str_replace($imagePath, '',$imgsrc);

        if($imagetype == 'galleryimage') {
            // get img from media repo and del image from system
            $imglist = $this->getDoctrine()->getRepository(TrnProductMedia::class)->findImgByFilename($imageName);

            if($fileUploaderHelper->removeFile($imageName)) {
                $this->getDoctrine()->getManager()->remove($imglist);
                $this->getDoctrine()->getManager()->flush();
            }
            $result = 'image deleted successfully';
        }
        return $this->json($result);
    }

}