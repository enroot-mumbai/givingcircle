<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstAreasInCity;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventRecurringDetails;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Entity\Transaction\TrnFundRaiserCircleEventDetails;
use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use App\Entity\Transaction\TrnMaterialInKindCircleEventCollectionCentre;
use App\Entity\Transaction\TrnMaterialInKindCircleEventDetails;
use App\Entity\Transaction\TrnMaterialInKindCircleEventSubEvents;
use App\Entity\Transaction\TrnProductMedia;
use App\Entity\Transaction\TrnVolunterCircleEventDetails;
use App\Entity\Transaction\TrnVolunterCircleEventOnSiteAddress;
use App\Entity\Transaction\TrnVolunterCircleEventSubEvents;
use App\Form\Master\MstAreasInCityType;
use App\Form\Transaction\TrnCircleEventsPortalType;
use App\Form\Transaction\TrnFundRaiserCircleEventDetailsPortalType;
use App\Form\Transaction\TrnMaterialInKindCircleEventDetailsPortalType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Form\Transaction\TrnVolunterCircleEventOnSiteAddressType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\FileUploaderHelper;
use App\Service\Mailer;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use App\Service\ProjectService;
use DateTime;
use DateTimeZone;
use Liip\ImagineBundle\Binary\Locator\FileSystemInsecureLocator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Service\CommonHelper;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class EventController
 * @IsGranted("ROLE_APP_USER")
 */
class EventController extends AbstractController
{

    /**
     * @Route("/create-an-event", name="create-an-event", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param FileUploaderHelper $fileUploaderHelper
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param MyAccountService $myAccountService
     * @return Response
     * @throws \Exception
     */
    public function createEvent(Request $request, SessionInterface $session, FileUploaderHelper $fileUploaderHelper,
                                TokenStorageInterface $tokenStorage, AppUserRepository $appUserRepository,
                                MyAccountService $myAccountService): Response
    {

        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);

        if($myAccountService->checkIfMandatoryFieldsFilled($appUser) == false) {
            $this->addFlash('error', 'Please update the Personal Info mandatory fields in order to proceed.');
            return $this->redirectToRoute('personal-info');
        }

        $trnCircleEvents = new TrnCircleEvents();
        $form = $this->createForm(TrnCircleEventsPortalType::class, $trnCircleEvents, array('mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated'])));
        $form->handleRequest($request);
        $session->remove('copyEventId');

        $arrPrimaryAISelected = array();
        $arrSecondaryAISelected = array();
        if($request->get('primaryAreaOfInterest') != null) {
            $arrPrimaryAISelected[] = $request->get('primaryAreaOfInterest');
            if($request->get('secondaryAreaOfInterest') != null) {
                $arrSecondaryAISelected[$request->get('primaryAreaOfInterest')][] = $request->get('secondaryAreaOfInterest');
            }
        }

        if($form->isSubmitted() && $form->isValid()){

            $file = $request->files;
            $entityManager = $this->getDoctrine()->getManager();
            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $arrSecondaryAI = explode(',', $secondaryAreaOfInterest);
            $objPrimaryAreaInterest = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find
            ($primaryAreaOfInterest);
            $objTrnAreaOfInterest = new TrnAreaOfInterest();
            $objTrnAreaOfInterest->setAreaInterestPrimary($objPrimaryAreaInterest);
            $objSecondaryAI = null;

            $trnCircleEvents->setMstCity($trnCircleEvents->getTrnCircle()->getMstCity());
            $trnCircleEvents->setMstState($trnCircleEvents->getTrnCircle()->getMstState());
            $trnCircleEvents->setMstCountry($trnCircleEvents->getTrnCircle()->getMstCountry());

            // get other approved/expired fund event count of user.
            $userFundsEvents = 0;
            $isFundEvent = false;
            foreach($trnCircleEvents->getMstEventProductType() as $evePrdType) {

                if($evePrdType->getEventProductType() == 'Fundraiser') {
                    $isFundEvent = true;
                    $userFundsEvents = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->getFundsEventByUser($userId);
                }
            }

            if($userFundsEvents > 0 || $isFundEvent == false) {
                // if any event there of user, set status to activated
                $trnCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Activated']));
            } else {
                // else set status to pending activation
                $trnCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Pending Activation']));
            }
            $trnCircleEvents->setCreatedOn(new \DateTime());
            $trnCircleEvents->setAppUser($appUser);
            $trnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnCircleEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')) );
            $trnCircleEvents->setIsActive(0);
            $entityManager->persist($trnCircleEvents);
            $entityManager->flush();
            foreach ($arrSecondaryAI as  $key => $nSecAI) {
                $objSecondaryAI = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI);
                $objTrnAreaOfInterest->addAreaInterestSecondary($objSecondaryAI);
            }
            $trnCircleEvents->addTrnAreaOfInterest($objTrnAreaOfInterest);

            if (!empty($file)) {
                $objProfileImageData = $request->files->get('profileImage');
                $objBackGroundImage = $request->files->get('backGroundImage');
                $arrImageGallery = $request->files->get('imageGallery');
                if (!empty($objProfileImageData) && !empty($objProfileImageData[0])) {
                    $objProfileImageData = $objProfileImageData[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objProfileImageData,
                        $trnCircleEvents->getName().
                        ' profileImage'.Uuid::uuid4()->toString(), null);
                    $trnCircleEvents->setProfileImage($newFilename);
                }
                if (!empty($objBackGroundImage) && !empty($objBackGroundImage[0])) {
                    $objBackGroundImage = $objBackGroundImage[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objBackGroundImage,
                        $trnCircleEvents->getName().
                        ' backGroundImage'.Uuid::uuid4()->toString(), null);
                    $trnCircleEvents->setBackgroundImagePath($newFilename);
                }
                $entityManager->persist($trnCircleEvents);
                if (!empty($arrImageGallery)) {
                    foreach ($arrImageGallery as $key => $objImageGallery) {
                        if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                            $objImageGallery = $objImageGallery[0];
                            $trnProductMedia = new TrnProductMedia();
                            $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                            $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                            $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                            $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                            $trnProductMedia->setMediaType('image');
                            $trnProductMedia->setMediaName('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaAltText('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaTitle('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setIsActive(1);
                            $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'GalleryImage'
                                .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                            $trnProductMedia->setMediaFileName($newFilename);
                            $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));
                            $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                            $trnProductMedia->setCreatedOn(new DateTime());
                            $entityManager->persist($trnProductMedia);
                            $trnCircleEvents->addTrnProductMedium($trnProductMedia);
                        }
                    }
                }
                $entityManager->flush();
            }
            $session->set('trnCircleEventId', $trnCircleEvents->getId());
            return $this->redirectToRoute('show-event-product-type-ui');
        }
        return $this->render('portal/event/create-an-event.html.twig', [
            'form' => $form->createView(),
            'arrPrimaryAISelected' => $arrPrimaryAISelected,
            'arrSecondaryAISelected' => $arrSecondaryAISelected
        ]);
    }

    /**
     * @Route("/edit-an-event", name="edit-an-event", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param FileUploaderHelper $fileUploaderHelper
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @return Response
     * @throws \Exception
     */
    public function editEvent(Request $request, SessionInterface $session, FileUploaderHelper $fileUploaderHelper,
                                TokenStorageInterface $tokenStorage, AppUserRepository $appUserRepository): Response
    {
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        $session->set('editEvent', true);

        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');
        $form = $this->createForm(TrnCircleEventsPortalType::class, $trnCircleEvents, array('mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated'])));
        $form->handleRequest($request);
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);
        $session->remove('copyEventId');

        $arrPrimaryAISelected = array();
        $arrSecondaryAISelected = array();
        if($request->get('primaryAreaOfInterest') != null) {
            $arrPrimaryAISelected[] = $request->get('primaryAreaOfInterest');
            if($request->get('secondaryAreaOfInterest') != null) {
                $arrSecondaryAISelected[$request->get('primaryAreaOfInterest')][] = $request->get('secondaryAreaOfInterest');
            }
        }
        $arrEventProductTypeId = $arrEventProductType = array();
        foreach ($trnCircleEvents->getMstEventProductType() as $MstEventProductType) {
            $arrEventProductType[] = $MstEventProductType->getEventProductType();
            $arrEventProductTypeId[] = $MstEventProductType->getId();
        }
        $arrTrnAreaOfInterests =  $trnCircleEvents->getTrnAreaOfInterests();
        $arrPrimaryAISelected = $arrSecondaryAISelected = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            $arrPrimaryAISelected[] = $TrnAreaOfInterest->getAreaInterestPrimary()->getId();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrSecondaryAISelected[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getId();
            }
        }

        $arrImages = array();
        if (!empty($trnCircleEvents->getProfileImage()) && $trnCircleEvents->getProfileImage() != 'files/') {
            $arrImages['profile'] = $trnCircleEvents->getProfileImage();
        }
        if (!empty($trnCircleEvents->getBackgroundImagePath()) && $trnCircleEvents->getBackgroundImagePath() != 'files/') {
            $arrImages['backgroundImage'] = $trnCircleEvents->getBackgroundImagePath();
        }
        foreach ($trnCircleEvents->getTrnProductMedia() as $trnProductMedia) {
            if (  strtolower($trnProductMedia->getMediaType()) == 'image' && $trnProductMedia->getMediaFileName()) {
                $arrImages['imageGallery'][] =  $trnProductMedia->getuploadedFilePath();
            }
        }

        if($form->isSubmitted() && $form->isValid()){
            $file = $request->files;
            $entityManager = $this->getDoctrine()->getManager();

            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $arrSecondaryAI = explode(',', $secondaryAreaOfInterest);

            $objPrimaryAreaInterest = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find
            ($primaryAreaOfInterest);

            $objTrnAreaOfInterest = new TrnAreaOfInterest();
            $objTrnAreaOfInterest->setAreaInterestPrimary($objPrimaryAreaInterest);
            $objSecondaryAI = null;

            $trnCircleEventId = $session->get('trnCircleEventId', array());
            if (empty($trnCircleEventId))
                return $this->redirectToRoute('create-an-event');
            $newCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
            if (empty($trnCircleEvents))
                return $this->redirectToRoute('create-an-event');

            // get other approved/expired fund event count of user.
            $userFundsEvents = 0;
            $isFundEvent = false;
            foreach($trnCircleEvents->getMstEventProductType() as $evePrdType) {

                if($evePrdType->getEventProductType() == 'Fundraiser') {
                    $isFundEvent = true;
                    $userFundsEvents = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->getFundsEventByUser($userId);
                }
            }
            if($userFundsEvents > 0 || $isFundEvent == false) {
                // if any event there of user, set status to activated
                $newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Activated']));
            } else {
                // else set status to pending activation
                $newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Pending Activation']));
            }
            /*$newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
            =>  'Activated']));*/

            $newCircleEvents->setCreatedOn(new \DateTime());
            $newCircleEvents->setAppUser($appUser);
            $newCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $newCircleEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')) );
            $newCircleEvents->setIsActive(0);
            //dd($newCircleEvents->getTrnVolunterCircleEventDetails()[0]->getTrnVolunterCircleEventSubEvents());
            $entityManager->persist($newCircleEvents);
            $entityManager->flush();

            // removeAreaInterestSecondary

            $existingSecondaryAI =  $trnCircleEvents->getTrnAreaOfInterests();
            $arrPrimaryAISelected = $arrSecondaryAISelected = array();

            foreach ($existingSecondaryAI as $TrnAreaOfInterest) {

                $this->getDoctrine()->getManager()->remove($TrnAreaOfInterest);
                $this->getDoctrine()->getManager()->flush();
            }

            foreach ($arrSecondaryAI as  $key => $nSecAI) {
                $objSecondaryAI = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI);
                $objTrnAreaOfInterest->addAreaInterestSecondary($objSecondaryAI);
            }
            //dd($objTrnAreaOfInterest);
            $newCircleEvents->addTrnAreaOfInterest($objTrnAreaOfInterest);

            if (!empty($file)) {
                $objProfileImageData = $request->files->get('profileImage');
                $objBackGroundImage = $request->files->get('backGroundImage');
                $arrImageGallery = $request->files->get('imageGallery');
                if (!empty($objProfileImageData) && !empty($objProfileImageData[0])) {
                    $objProfileImageData = $objProfileImageData[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objProfileImageData,
                        $trnCircleEvents->getName().
                        ' profileImage'.Uuid::uuid4()->toString(), null);
                    $newCircleEvents->setProfileImage($newFilename);
                }
                if (!empty($objBackGroundImage) && !empty($objBackGroundImage[0])) {
                    $objBackGroundImage = $objBackGroundImage[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objBackGroundImage,
                        $trnCircleEvents->getName().
                        ' backGroundImage'.Uuid::uuid4()->toString(), null);
                    $newCircleEvents->setBackgroundImagePath($newFilename);
                }
                $entityManager->persist($trnCircleEvents);
                if (!empty($arrImageGallery)) {
                    foreach ($arrImageGallery as $key => $objImageGallery) {
                        if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                            $objImageGallery = $objImageGallery[0];
                            $trnProductMedia = new TrnProductMedia();
                            $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                            $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                            $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                            $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                            $trnProductMedia->setMediaType('image');
                            $trnProductMedia->setMediaName('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaAltText('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaTitle('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setIsActive(1);
                            $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'GalleryImage'
                                .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                            $trnProductMedia->setMediaFileName($newFilename);
                            $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));
                            $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                            $trnProductMedia->setCreatedOn(new DateTime());
                            $entityManager->persist($trnProductMedia);
                            $newCircleEvents->addTrnProductMedium($trnProductMedia);
                        }
                    }
                }
                $entityManager->flush();
            }
            $session->set('trnCircleEventId', $newCircleEvents->getId());
            //$session->remove('copyEventId');
            return $this->redirectToRoute('show-event-product-type-ui');
        }


        return $this->render('portal/event/create-an-event-copied.html.twig', [
            'data' => $trnCircleEvents, 'form' => $form->createView(), 'arrEventProductType' => $arrEventProductType,
            'arrPrimaryAISelected' => $arrPrimaryAISelected, 'arrSecondaryAISelected' => $arrSecondaryAISelected,
            'arrEventProductTypeId' => $arrEventProductTypeId, 'arrImages' => $arrImages
        ]);


    }

    /**
     * @Route("/show-event-product-type-ui", name="show-event-product-type-ui", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return void
     */
    public function showEventProductTypeUI(Request $request, SessionInterface $session, NotificationService $notificationService) {
        $trnCircleEventId = $session->get('trnCircleEventId', array());

        if(!empty($request->get('mode')) && $request->get('mode') == 'edit') {
            $session->set('editEvent', true);
        }

        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $trnCircleEventCopyId = $session->get('copyEventId', array());
        $trnCircleEventCopy = null;
        if (!empty($trnCircleEventCopyId)){
            $trnCircleEventCopy = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventCopyId);
        }
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');
        foreach ($trnCircleEvents->getMstEventProductType() as $mstEventProductType){
            $arrEventProductType[] = $mstEventProductType->getEventProductType();
        }
        $trnCircle = $trnCircleEvents->getTrnCircle();

        $nCurrentIndex = 0;
        if ($request->query->has('currentIndex')) {
            $nCurrentIndex = $request->get('currentIndex');
        }
        $strEventProductType =  str_ireplace(array("(", ")", " "), array("","",""), strtolower($arrEventProductType[$nCurrentIndex]));
        switch ($strEventProductType) {
            case 'volunteerintime': {
                $trnVolunterCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();
                if (!empty($trnVolunterCircleEventDetailsArray) && !empty($trnVolunterCircleEventDetailsArray[0])) {
                    $trnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsArray[0];
                } else {
                    $trnVolunterCircleEventDetails =  new TrnVolunterCircleEventDetails();
                    $trnVolunterCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                }

                $trnVolunterCircleEventOnSiteAddresses = null;
                $trnVolunterCircleEventSubEvents = array();
                $trnCircleEventRecurringDetails = null;
                if (!empty($trnCircleEventCopy) && $request->isMethod('GET')) {
                    foreach ($trnCircleEventCopy->getTrnVolunterCircleEventDetails() as $trnVolunterCircleEventDetailsCopy) {
                        $trnVolunterCircleEventDetails->setTrnCircleEvents($trnVolunterCircleEventDetailsCopy->getTrnCircleEvents());
                        $trnVolunterCircleEventDetails->setWorkDescription($trnVolunterCircleEventDetailsCopy->getWorkDescription());
                        $trnVolunterCircleEventDetails->setKeyResponsibility($trnVolunterCircleEventDetailsCopy->getKeyResponsibility());
                        $trnVolunterCircleEventDetails->setOrgCompany($trnVolunterCircleEventDetailsCopy->getOrgCompany());
                        $trnVolunterCircleEventDetails->setMstPlaceOfWork($trnVolunterCircleEventDetailsCopy->getMstPlaceOfWork());
                        $trnVolunterCircleEventDetails->setMstEventOccurrence($trnVolunterCircleEventDetailsCopy->getMstEventOccurrence());
                        $trnVolunterCircleEventDetails->setAppUser($trnVolunterCircleEventDetailsCopy->getAppUser());
                        foreach ($trnVolunterCircleEventDetailsCopy->getMstSkillSet() as $mstSkillSet) {
                            $trnVolunterCircleEventDetails->addMstSkillSet($mstSkillSet);
                        }
                        $trnVolunterCircleEventDetails->setFromTime($trnVolunterCircleEventDetailsCopy->getFromTime());
                        $trnVolunterCircleEventDetails->setToTime($trnVolunterCircleEventDetailsCopy->getToTime());
                        $trnVolunterCircleEventDetails->setFromDate($trnVolunterCircleEventDetailsCopy->getFromDate());
                        $trnVolunterCircleEventDetails->setToDate($trnVolunterCircleEventDetailsCopy->getToDate());
                        foreach ($trnVolunterCircleEventDetailsCopy->getTrnVolunterCircleEventOnSiteAddresses() as
                                 $TrnVolunterCircleEventOnSiteAddressesCopy ) {
                            $trnVolunterCircleEventOnSiteAddresses = $TrnVolunterCircleEventOnSiteAddressesCopy;
                        }
                        foreach ($trnVolunterCircleEventDetailsCopy->getTrnCircleEventRecurringDetails() as
                                 $TrnCircleEventRecurringDetailsCopy ) {
                            $trnCircleEventRecurringDetails = $TrnCircleEventRecurringDetailsCopy;
                        }
                        foreach ($trnVolunterCircleEventDetailsCopy->getTrnVolunterCircleEventSubEvents() as $TrnVolunterCircleEventSubEventsCopy ) {
                            $trnVolunterCircleEventSubEvents[] = $TrnVolunterCircleEventSubEventsCopy;
                        }
                    }
                } else {

                    foreach ($trnCircleEvents->getTrnVolunterCircleEventDetails() as $trnVolunterCircleEventDetails) {

                        //$trnVolunterCircleEventOnSiteAddresses = $trnVolunterCircleEventDetails->getTrnVolunterCircleEventOnSiteAddresses();
                        foreach ($trnVolunterCircleEventDetails->getTrnVolunterCircleEventOnSiteAddresses() as
                                 $TrnVolunterCircleEventOnSiteAddresses ) {
                            $trnVolunterCircleEventOnSiteAddresses = $TrnVolunterCircleEventOnSiteAddresses;
                        }
                        //$trnCircleEventRecurringDetails = $trnVolunterCircleEventDetails->getTrnCircleEventRecurringDetails();
                        foreach ($trnVolunterCircleEventDetails->getTrnCircleEventRecurringDetails() as
                                 $TrnCircleEventRecurringDetailsCopy ) {
                            $trnCircleEventRecurringDetails = $TrnCircleEventRecurringDetailsCopy;
                        }
                        foreach ($trnVolunterCircleEventDetails->getTrnVolunterCircleEventSubEvents() as $TrnVolunterCircleEventSubEventsCopy ) {
                            $trnVolunterCircleEventSubEvents[] = $TrnVolunterCircleEventSubEventsCopy;
                        }
                    }

                }

//dd($trnVolunterCircleEventDetails);

                $form = $this->createForm(TrnVolunterCircleEventDetailsPortalType::class, $trnVolunterCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {

                    $noHours = $request->get('noHours');
                    $skillSet = $request->get('skillSet');
                    $hdnSubEventType = $request->get('hdnSubEventType');
                    $recurringBy = $request->get('recurringBy');
                    $recurringNosOfHours = $request->get('recurringNosOfHours');
                    $placeOfWOrk = $request->get('placeOfWOrk');
                    $selectedDatesWeekly = $request->get('selectedDatesWeekly');
                    $selectedDatesMonthly = $request->get('selectedDatesMonthly');
                    $toTime = $request->get('trn_volunter_circle_event_details_portal')['toTime'];


                    if (!empty($placeOfWOrk) && $placeOfWOrk != 'Virtual') {
                        $eventOnSiteAddress1 = $request->get('eventOnSiteAddress1');
                        $eventOnSiteLat = round($request->get('eventOnSiteLatitude'), 10);
                        $eventOnSiteLong = round($request->get('eventOnSiteLongitude'), 10);

                        $eventOnSiteAddress2 = '';
                        $eventOnSitePinCode = '';
                        $eventOnSiteCountry = '';
                        $eventOnSiteState = '';
                        $eventOnSiteCity = '';
                        $eventOnSiteAreaInCity = '';

                        /*$eventOnSiteAddress2 = $request->get('eventOnSiteAddress2');
                        $eventOnSitePinCode = $request->get('eventOnSitePinCode');
                        $eventOnSiteCountry = $request->get('eventOnSiteCountry');
                        $eventOnSiteState = $request->get('eventOnSiteState');
                        $eventOnSiteCity = $request->get('eventOnSiteCity');
                        $eventOnSiteAreaInCity = $request->get('eventOnSiteAreaInCity');*/
                    } else {
                        $eventOnSiteAddress1 = '';
                        $eventOnSiteLat = '';
                        $eventOnSiteLong = '';
                        $eventOnSiteAddress2 = '';
                        $eventOnSitePinCode = '';
                        $eventOnSiteCountry = '';
                        $eventOnSiteState = '';
                        $eventOnSiteCity = '';
                        $eventOnSiteAreaInCity = '';
                    }

                    $trnVolunterCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $mstEventOccurrence = $trnVolunterCircleEventDetails->getMstEventOccurrence();
                    $trnVolunterCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
                    $trnVolunterCircleEventDetails->setAppUser($appUser);
                    $trnVolunterCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnVolunterCircleEventDetails->setCreatedOn(new \DateTime());
                    $trnVolunterCircleEventDetails->setToTime(new \DateTime($toTime.':00'));
//dd($trnVolunterCircleEventDetails);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($trnVolunterCircleEventDetails);
                    $entityManager->flush();

                    $trnCircleEvents->addTrnVolunterCircleEventDetail($trnVolunterCircleEventDetails);
                    /*
                     * Remove any Previous Data If Any
                     * */
                    $arrTrnCircleEventRecurringDetail =
                        $trnVolunterCircleEventDetails->getTrnCircleEventRecurringDetails();
                    foreach ($arrTrnCircleEventRecurringDetail as $trnCircleEventRecurringDetail) {
                        $trnVolunterCircleEventDetails->removeTrnCircleEventRecurringDetail($trnCircleEventRecurringDetail);
                    }
                    $arrTrnVolunterCircleEventOnSiteAddresses = $trnVolunterCircleEventDetails->getTrnVolunterCircleEventOnSiteAddresses();
                    foreach ($arrTrnVolunterCircleEventOnSiteAddresses as $trnVolunterCircleEventOnSiteAddresses) {
                        $trnVolunterCircleEventDetails->removeTrnVolunterCircleEventOnSiteAddress($trnVolunterCircleEventOnSiteAddresses);
                    }
                    $entityManager->persist($trnVolunterCircleEventDetails);
                    $entityManager->flush();
                    /*
                     * Remove any Previous Data if any
                     * */
                    $tmpArr = $trnVolunterCircleEventDetails->getTrnVolunterCircleEventSubEvents();
                    foreach ($tmpArr as $arritem) {
                        $trnVolunterCircleEventDetails->removeTrnVolunterCircleEventSubEvent($arritem);
                    }

                    foreach ($hdnSubEventType as $subEvent) {
                        $subEventSlug = str_replace(' ','_', strtolower($subEvent));

                        $objTrnVolunterCircleEventSubEvents = new TrnVolunterCircleEventSubEvents();
                        $objTrnVolunterCircleEventSubEvents->setIsActive(1);
                        $objTrnVolunterCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $objTrnVolunterCircleEventSubEvents->setAppUser($appUser);
                        $objTrnVolunterCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $objTrnVolunterCircleEventSubEvents->setSubEventName($subEvent);
                        $objTrnVolunterCircleEventSubEvents->setNumberOfHours($noHours[$subEventSlug]);
                        $objTrnVolunterCircleEventSubEvents->setSubEventReccuringBy($skillSet[$subEventSlug]);
                        $objTrnVolunterCircleEventSubEvents->setTrnVolunterCircleEventDetails($trnVolunterCircleEventDetails);
                        $entityManager->persist($objTrnVolunterCircleEventSubEvents);
                        $trnVolunterCircleEventDetails->addTrnVolunterCircleEventSubEvent($objTrnVolunterCircleEventSubEvents);
                    }

                    if (!empty($mstEventOccurrence) && $mstEventOccurrence->getEventOccurrence() == 'Recurring'){

                        $trnCircleEventRecurringDetails = new TrnCircleEventRecurringDetails();
                        $trnCircleEventRecurringDetails->setAppUser($appUser);
                        $trnCircleEventRecurringDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnCircleEventRecurringDetails->setCreatedOn(new \DateTime());
                        $trnCircleEventRecurringDetails->setIsActive(true);
                        $trnCircleEventRecurringDetails->setTrnCircleEvents($trnCircleEvents);
                        $trnCircleEventRecurringDetails->setTrnVolunterCircleEventDetails($trnVolunterCircleEventDetails);
                        $trnCircleEventRecurringDetails->setMstRecurringBy($this->getDoctrine()->getRepository(MstRecurringBy::class)->findOneBy(array('recurringBy' => $recurringBy)));
                        $trnCircleEventRecurringDetails->setNumberOfHours($recurringNosOfHours);
                        if($recurringBy == 'Monthly') {
                            // Monthly
                            $trnCircleEventRecurringDetails->setDatesSelected($selectedDatesMonthly);
                            $entityManager->persist($trnCircleEventRecurringDetails);
                        } else if($recurringBy == 'Weekly') {
                            $arrExplodeDays = explode(",", $selectedDatesWeekly);
                            foreach ($arrExplodeDays as $day) {
                                $trnCircleEventRecurringDetails->addMstDaysOfWeek(
                                    $this->getDoctrine()->getRepository(MstDaysOfWeek::class)->findOneBy(array('dayOfWeek' => $day)));
                            }
                            $entityManager->persist($trnCircleEventRecurringDetails);
                            // Weekly
                            $trnVolunterCircleEventDetails->addTrnCircleEventRecurringDetail($trnCircleEventRecurringDetails);
                        } else {
                            $entityManager->persist($trnCircleEventRecurringDetails);
                            // Daily
                            $trnVolunterCircleEventDetails->addTrnCircleEventRecurringDetail($trnCircleEventRecurringDetails);
                        }
                    }

                    if (!empty($placeOfWOrk) && $placeOfWOrk == 'OnSite') {

                        $trnVolunterCircleEventOnSiteAddresses = new TrnVolunterCircleEventOnSiteAddress();
                        $trnVolunterCircleEventOnSiteAddresses->setAppUser($appUser);
                        $trnVolunterCircleEventOnSiteAddresses->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnVolunterCircleEventOnSiteAddresses->setCreatedOn(new \DateTime());
                        $trnVolunterCircleEventOnSiteAddresses->setIsActive(true);
                        $trnVolunterCircleEventOnSiteAddresses->setTrnVolunterCircleEventDetails($trnVolunterCircleEventDetails);
                        $trnVolunterCircleEventOnSiteAddresses->setEventOnSiteAddress1($eventOnSiteAddress1);
                        $trnVolunterCircleEventOnSiteAddresses->setEventOnSiteAddress2($eventOnSiteAddress2);
                        $trnVolunterCircleEventOnSiteAddresses->setEventOnSitePinCode($eventOnSitePinCode);
                        $trnVolunterCircleEventOnSiteAddresses->setMstCountry($this->getDoctrine()->getRepository(MstCountry::class)->find($eventOnSiteCountry));
                        $trnVolunterCircleEventOnSiteAddresses->setMstState($this->getDoctrine()->getRepository(MstState::class)->find($eventOnSiteState));
                        $trnVolunterCircleEventOnSiteAddresses->setMstCity($this->getDoctrine()->getRepository(MstCity::class)->find($eventOnSiteCity));
                        $trnVolunterCircleEventOnSiteAddresses->setMstAreasInCity($this->getDoctrine()->getRepository(MstAreasInCity::class)->find($eventOnSiteAreaInCity));
                        $trnVolunterCircleEventOnSiteAddresses->setEventOnSiteLocationLatitude($eventOnSiteLat);
                        $trnVolunterCircleEventOnSiteAddresses->setEventOnSiteLocationLongitude($eventOnSiteLong);
                        $entityManager->persist($trnVolunterCircleEventOnSiteAddresses);
                        $trnVolunterCircleEventDetails->addTrnVolunterCircleEventOnSiteAddress($trnVolunterCircleEventOnSiteAddresses);
                    }

                    $entityManager->flush();

                    if($request->get('submission_type') == 'review') {
                        return $this->redirectToRoute('create-an-event-review');
                    } elseif($request->get('submission_type') == 'submit') {
                        return $this->redirectToRoute('create-an-event-thank-you');
                    } else {
                        // continue - i.e. current behaviour
                        if( $nCurrentIndex == count($arrEventProductType) - 1 )
                            // ideally this case will not come
                            return $this->redirectToRoute('create-an-event-review');
                        else{
                            $nCurrentIndex = $nCurrentIndex + 1;
                            return $this->redirectToRoute('show-event-product-type-ui', array('currentIndex' => $nCurrentIndex));
                        }
                    }

                    /*if( $nCurrentIndex == count($arrEventProductType) - 1 )
                        return $this->redirectToRoute('create-an-event-review');
                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('show-event-product-type-ui', array('currentIndex' => $nCurrentIndex));
                    }*/
                }

                $arrHours = range(1, 12);

                /*$onSiteAddress = new TrnVolunterCircleEventOnSiteAddress;
                $onSiteAddressForm = $this->createForm(TrnVolunterCircleEventOnSiteAddressType::class, $onSiteAddress);
                //, 'onSiteAddressForm' => $onSiteAddressForm->createView()
                */

                return $this->render('portal/event/create-an-event-volunteer.html.twig', ['form' => $form->createView(),
                    'arrEventProductType' => $arrEventProductType, 'currentIndex' => $nCurrentIndex, 'totalEventTypes' => count($arrEventProductType), 'arrHours' =>
                        $arrHours, 'trnVolunterCircleEventDetails' => $trnVolunterCircleEventDetails,
                    'trnVolunterCircleEventOnSiteAddresses' => $trnVolunterCircleEventOnSiteAddresses,
                    'trnVolunterCircleEventSubEvents' => $trnVolunterCircleEventSubEvents,
                    'trnCircleEventRecurringDetails' => $trnCircleEventRecurringDetails]);
                break;
            }
            case 'materialinkind' :{
                $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
                if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
                    $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
                }
                else{
                    $trnMaterialInKindCircleEventDetails =  new TrnMaterialInKindCircleEventDetails();
                    $trnMaterialInKindCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                }

                $eventSubEvents = array();
                $collectionCenter1  = array();
                $collectionCenterDetails = array();
                $trnCircleEventRecurringDetails = null;
                if( (!empty($trnCircleEventCopy) && $request->isMethod('GET')) ) {
                    foreach ($trnCircleEventCopy->getTrnMaterialInKindCircleEventDetails() as $trnMaterialInKindCircleEventDetailsCopy) {
                        $trnMaterialInKindCircleEventDetails->setTrnCircleEvents($trnMaterialInKindCircleEventDetailsCopy->getTrnCircleEvents());
                        $trnMaterialInKindCircleEventDetails->setOrgCompany($trnMaterialInKindCircleEventDetailsCopy->getOrgCompany());
                        $trnMaterialInKindCircleEventDetails->setAppUser($trnMaterialInKindCircleEventDetailsCopy->getAppUser());
                        $trnMaterialInKindCircleEventDetails->setFromDate($trnMaterialInKindCircleEventDetailsCopy->getFromDate());
                        $trnMaterialInKindCircleEventDetails->setToDate($trnMaterialInKindCircleEventDetailsCopy->getToDate());
                        foreach ($trnMaterialInKindCircleEventDetailsCopy->getTrnMaterialInKindCircleEventSubEvents()
                                 as $TrnMaterialInKindCircleEventSubEvents) {
                            $eventSubEvents[] = array(
                                'subEventName' => $TrnMaterialInKindCircleEventSubEvents->getSubEventName(),
                                'itemName' => $TrnMaterialInKindCircleEventSubEvents->getItemName(),
                                'itemQuantity' => $TrnMaterialInKindCircleEventSubEvents->getItemQuantity(),
                                'unit' => $TrnMaterialInKindCircleEventSubEvents->getUnit(),
                                );
                        }
                        foreach ($trnMaterialInKindCircleEventDetailsCopy->getTrnMaterialInKindCircleEventCollectionCentres()
                                 as $TrnMaterialInKindCircleEventCollectionCentres) {
                            $collectionCenter1[] = $TrnMaterialInKindCircleEventCollectionCentres->getTrnCollectionCentreDetails()->getId();
                        }
                    }
                } else if (!empty($trnMaterialInKindCircleEventDetails)) {
                    // if edit, then details will be there
                    foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventSubEvents()
                             as $TrnMaterialInKindCircleEventSubEvents) {
                        $eventSubEvents[] = array(
                            'subEventName' => $TrnMaterialInKindCircleEventSubEvents->getSubEventName(),
                            'itemName' => $TrnMaterialInKindCircleEventSubEvents->getItemName(),
                            'itemQuantity' => $TrnMaterialInKindCircleEventSubEvents->getItemQuantity(),
                            'unit' => $TrnMaterialInKindCircleEventSubEvents->getUnit(),
                        );
                    }
                    foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventCollectionCentres()
                             as $TrnMaterialInKindCircleEventCollectionCentres) {

                        $trnCCDetailId = $TrnMaterialInKindCircleEventCollectionCentres->getTrnCollectionCentreDetails()->getId();

                        $collectionCenter1[] = $trnCCDetailId;
                        $collectionCenterDetails[$trnCCDetailId] = $TrnMaterialInKindCircleEventCollectionCentres;
                    }
                }

//dd($collectionCenterDetails);
                $form = $this->createForm(TrnMaterialInKindCircleEventDetailsPortalType::class, $trnMaterialInKindCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);
//dd($request);
                    $itemRequired = $request->get('itemRequired');
                    $quantity = $request->get('quantity');
                    $unit = $request->get('unit');
                    $remarks = $request->get('remarks');
                    $FromDate = $request->get('FromDate');
                    $ToDate = $request->get('ToDate');
                    $collectionCenterType = $request->get('collectionCenterType');

                    $collectionCenterArray = array();
                    if($collectionCenterType == 'CreateNew') {

                        foreach ($request->get('collectionCenterFirstName') as $key=>$firstname ) {
                            $tmpArray = array();
                            $tmpArray['firstname'] = $firstname;
                            $tmpArray['lastname'] = $request->get('collectionCenterLastName')[$key];
                            $tmpArray['collectionCenterAddress1'] = $request->get('collectionCenterAddress1')[$key];
                            $tmpArray['collectionCenterAddress2'] = $request->get('collectionCenterAddress2')[$key];
                            $tmpArray['collectionCenterPinCode'] = $request->get('collectionCenterPinCode')[$key];
                            $tmpArray['collectionCenterCountry'] = $request->get('collectionCenterCountry')[$key];
                            $tmpArray['collectionCenterState'] = $request->get('collectionCenterState')[$key];
                            $tmpArray['collectionCenterCity'] = $request->get('collectionCenterCity')[$key];
                            $tmpArray['collectionCenterStartTime'] = $request->get('collectionCenterStartTime')[$key];
                            $tmpArray['collectionCenterEndTime'] = $request->get('collectionCenterEndTime')[$key];
                            $tmpArray['collectionCenterOpenOnDays'] = $request->get('collectionCenterOpenOnDays')[$key];
                            $tmpArray['collectionCenterFromDate'] = $request->get('collectionCenterFromDate')[$key];
                            $tmpArray['collectionCenterToDate'] = $request->get('collectionCenterToDate')[$key];

                            $collectionCenterArray[] = $tmpArray;
                        }
                    } else {
                        // From master > $key is the Collection Center ID
                        foreach ($request->get('startTime') as $key=>$startTime ) {
                            $tmpArray = array();
                            $tmpArray['collectionCenterStartTime'] = $startTime;
                            $tmpArray['collectionCenterEndTime'] = $request->get('endTime')[$key];
                            $tmpArray['collectionCenterOpenOnDays'] = $request->get('collectionCenterOpenOnDaysMst')[$key];
                            $tmpArray['collectionCenterFromDate'] = $request->get('FromDate')[$key];
                            $tmpArray['collectionCenterToDate'] = $request->get('ToDate')[$key];

                            $collectionCenterArray[$key] = $tmpArray;
                        }
                    }

                    $trnMaterialInKindCircleEventDetails->setAppUser($appUser);
                    $trnMaterialInKindCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnMaterialInKindCircleEventDetails->setCreatedOn(new \DateTime());

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($trnMaterialInKindCircleEventDetails);
                    $entityManager->flush();
                    $trnCircleEvents->addTrnMaterialInKindCircleEventDetails($trnMaterialInKindCircleEventDetails);
                    $entityManager->persist($trnCircleEvents);
                    $entityManager->flush();
                    /*
                     * Remove any Previous Data If Any
                     * */
                    $arrTrnMaterialInKindCircleEventSubEvents =
                        $trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventSubEvents();
                    foreach ($arrTrnMaterialInKindCircleEventSubEvents as $trnMaterialInKindCircleEventSubEvents) {
                        $trnMaterialInKindCircleEventDetails->removeTrnMaterialInKindCircleEventSubEvent($trnMaterialInKindCircleEventSubEvents);
                    }
                    $arrTrnMaterialReceivedAtCollectionCentres = $trnMaterialInKindCircleEventDetails->getTrnMaterialReceivedAtCollectionCentres();
                    foreach ($arrTrnMaterialReceivedAtCollectionCentres as $trnMaterialReceivedAtCollectionCentres) {
                        $trnMaterialInKindCircleEventDetails->removeTrnMaterialReceivedAtCollectionCentre($trnMaterialReceivedAtCollectionCentres);
                    }
                    $entityManager->persist($trnMaterialInKindCircleEventDetails);
                    $entityManager->flush();
                    /*
                     * Remove any Previous Data if any
                     * */
                    $collectionCenter = $session->get('collectionCenter', array());

                    foreach ($itemRequired as $key => $strItemRequired) {
                        $trnMaterialInKindCircleEventSubEvents = new TrnMaterialInKindCircleEventSubEvents();
                        $trnMaterialInKindCircleEventSubEvents->setSubEventName($remarks[$key]);
                        $trnMaterialInKindCircleEventSubEvents->setItemName($strItemRequired);
                        $trnMaterialInKindCircleEventSubEvents->setUnit($unit[$key]);
                        $trnMaterialInKindCircleEventSubEvents->setItemQuantity($quantity[$key]);
                        $trnMaterialInKindCircleEventSubEvents->setTrnMaterialInKindCircleEventDetails($trnMaterialInKindCircleEventDetails);

                        $trnMaterialInKindCircleEventSubEvents->setAppUser($appUser);
                        $trnMaterialInKindCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnMaterialInKindCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $trnMaterialInKindCircleEventSubEvents->setIsActive(true);
                        $entityManager->persist($trnMaterialInKindCircleEventSubEvents);
                        $trnMaterialInKindCircleEventDetails->addTrnMaterialInKindCircleEventSubEvent($trnMaterialInKindCircleEventSubEvents);
                    }

                    // Remove previously added collection centers
                    foreach($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventCollectionCentres() as $center) {

                        $this->getDoctrine()->getManager()->remove($center);
                        $this->getDoctrine()->getManager()->flush();

                    }

                    if(count($collectionCenter) > 0 ) {

                        // if from master, array will be saved for this event
                        foreach ($collectionCenterArray as $key=>$centerArray) {
                            $arrTrnCollectionCentreDetails = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)
                                ->findOneBy(['id' => $key]);

                                $trnCollectionCentreDetailsObj = clone $arrTrnCollectionCentreDetails;
                                $trnCollectionCentreDetailsObj->setTrnCircle($trnCircle);
                                $trnCollectionCentreDetailsObj->setTrnCircleEvent($trnCircleEvents);
                                $trnCollectionCentreDetailsObj->setAppUser($appUser);
                                $trnCollectionCentreDetailsObj->setModeOfInsertion('Master');
                                $trnCollectionCentreDetailsObj->setStartTime(new \DateTime($centerArray['collectionCenterStartTime']));
                                $trnCollectionCentreDetailsObj->setEndTime(new \DateTime($centerArray['collectionCenterEndTime']));
                                $entityManager->persist($trnCollectionCentreDetailsObj);


                                // remove Old
                                foreach ($trnCollectionCentreDetailsObj->getMstDaysOfWeek() as $daysObj) {
                                    $trnCollectionCentreDetailsObj->removeMstDaysOfWeek($daysObj);
                                }

                                $openOnDaysArray = explode(',',$centerArray['collectionCenterOpenOnDays']);
                                foreach ($openOnDaysArray as $dayname) {
                                    $trnCollectionCentreDetailsObj->addMstDaysOfWeek($this->getDoctrine()->getRepository(MstDaysOfWeek::class)->findOneBy(['dayOfWeek' => $dayname]));
                                }

                                $objTrnMaterialInKindCircleEventCollectionCentre = new TrnMaterialInKindCircleEventCollectionCentre();
                                $objTrnMaterialInKindCircleEventCollectionCentre->setTrnCollectionCentreDetails($trnCollectionCentreDetailsObj);
                                $objTrnMaterialInKindCircleEventCollectionCentre->setAppUser($appUser);
                                $objTrnMaterialInKindCircleEventCollectionCentre->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                                $objTrnMaterialInKindCircleEventCollectionCentre->setCreatedOn(new \DateTime());
                                $objTrnMaterialInKindCircleEventCollectionCentre->setFromDate(new \DateTime($FromDate[$key]));
                                $objTrnMaterialInKindCircleEventCollectionCentre->setToDate(new \DateTime($ToDate[$key]));
                                $objTrnMaterialInKindCircleEventCollectionCentre->setIsActive(true);
                                $entityManager->persist($objTrnMaterialInKindCircleEventCollectionCentre);
                                $trnMaterialInKindCircleEventDetails->addTrnMaterialInKindCircleEventCollectionCentre($objTrnMaterialInKindCircleEventCollectionCentre);
                        }

                    } else if($collectionCenterType == 'CreateNew') {

                        foreach ($collectionCenterArray as $centerArray) {
                            // add to TrnCollectionCentreDetails with isActive 0 change the status in thank you page
                            $trnCollectionCentreDetailsObj = new TrnCollectionCentreDetails();
                            $trnCollectionCentreDetailsObj->setTrnCircle($trnCircle);
                            $trnCollectionCentreDetailsObj->setTrnCircleEvent($trnCircleEvents);
                            $trnCollectionCentreDetailsObj->setAppUser($appUser);
                            $trnCollectionCentreDetailsObj->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $trnCollectionCentreDetailsObj->setFirstName($centerArray['firstname']);
                            $trnCollectionCentreDetailsObj->setLastName($centerArray['lastname']);
                            $trnCollectionCentreDetailsObj->setAddress1($centerArray['collectionCenterAddress1']);
                            $trnCollectionCentreDetailsObj->setAddress2($centerArray['collectionCenterAddress2']);
                            $trnCollectionCentreDetailsObj->setPinCode($centerArray['collectionCenterPinCode']);
                            $trnCollectionCentreDetailsObj->setMstCountry($this->getDoctrine()->getRepository(MstCountry::class)->find($centerArray['collectionCenterCountry']));
                            $trnCollectionCentreDetailsObj->setMstState($this->getDoctrine()->getRepository(MstState::class)->find($centerArray['collectionCenterState']));
                            $trnCollectionCentreDetailsObj->setMstCity($this->getDoctrine()->getRepository(MstCity::class)->find($centerArray['collectionCenterCity']));
                            $trnCollectionCentreDetailsObj->setStartTime(new \DateTime($centerArray['collectionCenterStartTime']));
                            $trnCollectionCentreDetailsObj->setEndTime(new \DateTime($centerArray['collectionCenterEndTime']));
                            $trnCollectionCentreDetailsObj->setCreatedOn(new \DateTime());
                            $trnCollectionCentreDetailsObj->setUserIpAddress($_SERVER['SERVER_ADDR']);
                            $trnCollectionCentreDetailsObj->setLatitude('');
                            $trnCollectionCentreDetailsObj->setLongitude('');
                            $trnCollectionCentreDetailsObj->setIsActive(0);
                            $trnCollectionCentreDetailsObj->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                            =>  'Pending Activation']));
                            $trnCollectionCentreDetailsObj->setModeOfInsertion('CreateNew');
                            $entityManager->persist($trnCollectionCentreDetailsObj);

                            $openOnDaysArray = explode(',',$centerArray['collectionCenterOpenOnDays']);
                            foreach ($openOnDaysArray as $dayname) {
                                $trnCollectionCentreDetailsObj->addMstDaysOfWeek($this->getDoctrine()->getRepository(MstDaysOfWeek::class)->findOneBy(['dayOfWeek' => $dayname]));
                            }

                            // set details in link table
                            $objTrnMaterialInKindCircleEventCollectionCentre = new TrnMaterialInKindCircleEventCollectionCentre();
                            $objTrnMaterialInKindCircleEventCollectionCentre->setTrnCollectionCentreDetails($trnCollectionCentreDetailsObj);
                            $objTrnMaterialInKindCircleEventCollectionCentre->setAppUser($appUser);
                            $objTrnMaterialInKindCircleEventCollectionCentre->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $objTrnMaterialInKindCircleEventCollectionCentre->setCreatedOn(new \DateTime());
                            $objTrnMaterialInKindCircleEventCollectionCentre->setFromDate(new \DateTime($centerArray['collectionCenterFromDate']));
                            $objTrnMaterialInKindCircleEventCollectionCentre->setToDate(new \DateTime($centerArray['collectionCenterToDate']));
                            $objTrnMaterialInKindCircleEventCollectionCentre->setIsActive(true);
                            $entityManager->persist($objTrnMaterialInKindCircleEventCollectionCentre);
                            $trnMaterialInKindCircleEventDetails->addTrnMaterialInKindCircleEventCollectionCentre($objTrnMaterialInKindCircleEventCollectionCentre);
                        }
                    }

                    $entityManager->flush();
                    $session->remove('collectionCenter'); // will be removed if set in session i.e. from master

                    if(count($collectionCenter) > 0 ) {
                        $arrTrnCollectionCentreDetails = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)
                            ->findBy(['id' => $collectionCenter]);
                        foreach ($arrTrnCollectionCentreDetails as $TrnCollectionCentreDetails) {
                            $notificationService->setAppUser($TrnCollectionCentreDetails->getAppUser());
                            $notificationService->setTrnCircle($trnCircle);
                            $notificationService->setTrnCircleEvents($trnCircleEvents);
                            $notificationService->doProcess('Material To Collection Centre');
                        }
                    }

                    if($request->get('submission_type') == 'review') {
                        return $this->redirectToRoute('create-an-event-review');
                    } elseif($request->get('submission_type') == 'submit') {
                        return $this->redirectToRoute('create-an-event-thank-you');
                    } else {
                        // continue - i.e. current behaviour
                        if( $nCurrentIndex == count($arrEventProductType) - 1 )
                            // ideally this case will not come
                            return $this->redirectToRoute('create-an-event-review');
                        else{
                            $nCurrentIndex = $nCurrentIndex + 1;
                            return $this->redirectToRoute('show-event-product-type-ui', array('currentIndex' => $nCurrentIndex));
                        }
                    }

                    /*if( $nCurrentIndex == count($arrEventProductType) - 1 )
                        return $this->redirectToRoute('create-an-event-review');
                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('show-event-product-type-ui', array('currentIndex' => $nCurrentIndex));
                    }*/
                }
                $session->remove('collectionCenter');

                $arrHours = range(1, 24);
                $arrMstDaysOfWeek = $this->getDoctrine()->getRepository(MstDaysOfWeek::class)->findAll();

                return $this->render('portal/event/create-an-event-material.html.twig', ['form' => $form->createView
                (), 'arrEventProductType' => $arrEventProductType, 'currentIndex' => $nCurrentIndex,
                    'totalEventTypes' => count($arrEventProductType), 'eventSubEvents'=> $eventSubEvents,
                'collectionCenter1'=> $collectionCenter1, 'trnCircleEventRecurringDetails' =>
                        $trnCircleEventRecurringDetails, 'collectionCenterDetails' => $collectionCenterDetails,
                    'arrHours' => $arrHours, 'circleId' => $trnCircle->getId(), 'arrMstDaysOfWeek' => $arrMstDaysOfWeek]);
                break;
            }
            case 'fundraiser':{
                $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
                if (!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])) {
                    $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
                }
                else {
                    $trnFundRaiserCircleEventDetails =  new TrnFundRaiserCircleEventDetails();
                    $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                }
                $fundRaiserSubEventsCopy = array();
                $fundCircleEventRecurringDetails = null;

                if (!empty($trnCircleEventCopy) && $request->isMethod('GET')) {
                    foreach ($trnCircleEventCopy->getTrnFundRaiserCircleEventDetails() as $trnFundRaiserCircleEventDetailsCopy) {
                        $trnFundRaiserCircleEventDetails->setTargetAmount($trnFundRaiserCircleEventDetailsCopy->getTargetAmount());
                        $trnFundRaiserCircleEventDetails->setMstCurrencyTargetAmount($trnFundRaiserCircleEventDetailsCopy->getMstCurrencyTargetAmount());
//                        $trnFundRaiserCircleEventDetails->setMinContributionAmount($trnFundRaiserCircleEventDetailsCopy->getMinContributionAmount());
//                        $trnFundRaiserCircleEventDetails->setMstCurrencyMinContribution($trnFundRaiserCircleEventDetailsCopy->getMstCurrencyMinContribution());
                        $trnFundRaiserCircleEventDetails->setPurposeOfRaisingFunds($trnFundRaiserCircleEventDetailsCopy->getPurposeOfRaisingFunds());
                        $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnFundRaiserCircleEventDetailsCopy->getTrnCircleEvents());
                        $trnFundRaiserCircleEventDetails->setOrgCompany($trnFundRaiserCircleEventDetailsCopy->getOrgCompany());
                        $trnFundRaiserCircleEventDetails->setAppUser($trnFundRaiserCircleEventDetailsCopy->getAppUser());
                        $trnFundRaiserCircleEventDetails->setFromDate($trnFundRaiserCircleEventDetailsCopy->getFromDate());
                        $trnFundRaiserCircleEventDetails->setToDate($trnFundRaiserCircleEventDetailsCopy->getToDate());
                        $trnFundRaiserCircleEventDetails->setMstEventOccurrence($trnFundRaiserCircleEventDetailsCopy->getMstEventOccurrence());
                        $trnFundRaiserCircleEventDetails->setIsDistributedEvent($trnFundRaiserCircleEventDetailsCopy->getIsDistributedEvent());
                        $trnFundRaiserCircleEventDetails->setIsUrgent($trnFundRaiserCircleEventDetailsCopy->getIsUrgent());
                        foreach ($trnFundRaiserCircleEventDetailsCopy->getTrnFundRaiserCircleEventSubEvents() as $TrnFundRaiserCircleEventSubEventsCopy) {
                            $fundRaiserSubEventsCopy[] = $TrnFundRaiserCircleEventSubEventsCopy;
                        }
                        foreach ($trnFundRaiserCircleEventDetailsCopy->getTrnCircleEventRecurringDetails() as $TrnCircleEventRecurringDetailsCopy) {
                            $fundCircleEventRecurringDetails = $TrnCircleEventRecurringDetailsCopy;
                        }
                    }
                } else if(!empty($trnFundRaiserCircleEventDetails)) {
                    // if in edit mode
                    foreach ($trnFundRaiserCircleEventDetails->getTrnFundRaiserCircleEventSubEvents() as $TrnFundRaiserCircleEventSubEvents) {
                        $fundRaiserSubEventsCopy[] = $TrnFundRaiserCircleEventSubEvents;
                    }
                    foreach ($trnFundRaiserCircleEventDetails->getTrnCircleEventRecurringDetails() as $TrnCircleEventRecurringDetails) {
                        $fundCircleEventRecurringDetails = $TrnCircleEventRecurringDetails;
                    }
                }
                $form = $this->createForm(TrnFundRaiserCircleEventDetailsPortalType::class,
                    $trnFundRaiserCircleEventDetails);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {

                    $isurgentVal = 0;
                    if(isset($request->request->get('trn_fund_raiser_circle_event_details_portal')['isUrgent'])) {
                        $isurgentVal = $request->request->get('trn_fund_raiser_circle_event_details_portal')['isUrgent'];
                    }
                    $user_id = $this->getUser()->getId();
                    $appUser = $this->getDoctrine()->getRepository(AppUser::class)->findOneBy(["id" => $user_id]);

                    $entityManager = $this->getDoctrine()->getManager();

                    if($request->request->has('accountHolderName') && $request->request->get('accountHolderName') != '' ) {

                        $bankdetailsId = '';
                        if($request->request->has('bankdetailsId')) {
                            $bankdetailsId = $request->request->get('bankdetailsId');
                        }

                        $bankName = $request->request->get('bankName');
                        $accountHolderName = $request->request->get('accountHolderName');
                        $accountNumber = $request->request->get('accountNumber');
                        $ifscCode = $request->request->get('ifscCode');
                        $objMstAccType = $this->getDoctrine()->getRepository(MstBankAccountType::class)->findOneBy(array('id' => $request->request->get('bankAccountType')));

                        // Add Bank Details to User
                        if(!empty($bankdetailsId)) {
                            $trnBankDetails = $this->getDoctrine()->getRepository(TrnBankDetails::class)->findOneBy(array('id' => $bankdetailsId));
                        } else {
                            $trnBankDetails = new TrnBankDetails();
                        }
                        $trnBankDetails->setBankName($bankName);
                        $trnBankDetails->setAccountHolderName($accountHolderName);
                        $trnBankDetails->setAccountNumber($accountNumber);
                        $trnBankDetails->setIfscCode($ifscCode);
                        $trnBankDetails->setMstBankAccountType($objMstAccType);
                        $trnBankDetails->setIsActive(0); // inactive as details need to get verified by GC
                        $appUser->addTrnBankDetail($trnBankDetails);
                        $entityManager->persist($appUser);

                        // Add the same details to project table as well
                        $objTrnCircle = $trnCircleEvents->getTrnCircle();
                        $objTrnCircle->setBeneficiaryBankName($bankName);
                        $objTrnCircle->setBeneficiaryAccountHolderName($accountHolderName);
                        $objTrnCircle->setBeneficiaryBankAccountNumber($accountNumber);
                        $objTrnCircle->setBeneficiaryIfscCode($ifscCode);
                        $objTrnCircle->setMstBankAccountTypeBeneficiary($objMstAccType);
                        $entityManager->persist($objTrnCircle);

                        $entityManager->flush();
                    }

                    $trnFundRaiserCircleEventDetails->setTrnCircleEvents($trnCircleEvents);
                    $trnFundRaiserCircleEventDetails->setAppUser($appUser);
                    $trnFundRaiserCircleEventDetails->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnFundRaiserCircleEventDetails->setCreatedOn(new \DateTime());
                    $trnFundRaiserCircleEventDetails->setIsUrgent($isurgentVal);
                    $entityManager->persist($trnFundRaiserCircleEventDetails);
                    $entityManager->flush();

                    $trnCircleEvents->addTrnFundRaiserCircleEventDetail($trnFundRaiserCircleEventDetails);
                    $trnCircleEvents->setIsUrgent($isurgentVal);
                    $entityManager->persist($trnCircleEvents);
                    $entityManager->flush();
                    /*
                     * Remove Previous Data if Any
                     * */
                    $arrTrnFundRaiserCircleEventSubEvents =
                        $trnFundRaiserCircleEventDetails->getTrnFundRaiserCircleEventSubEvents();
                    foreach ($arrTrnFundRaiserCircleEventSubEvents as $TrnFundRaiserCircleEventSubEvents) {
                        $trnFundRaiserCircleEventDetails->removeTrnFundRaiserCircleEventSubEvent($TrnFundRaiserCircleEventSubEvents);

                    }
                    $entityManager->persist($trnFundRaiserCircleEventDetails);
                    $entityManager->flush();
                    /*
                     * Remove Previous Data if Any
                     * */
                    $fundRaiserSubEvents = $session->get('fundRaiserSubEvents', array());

                    foreach ($fundRaiserSubEvents as $data) {
                        $trnFundRaiserCircleEventSubEvents = new TrnFundRaiserCircleEventSubEvents();
                        $trnFundRaiserCircleEventSubEvents->setSubEventName($data['subEventTimePeriodSupported']. ' '
                            .$data['subEventNoOfBeneficiaries']. ' '.$data['subEventCurrency']. ' '.$data['subEventAmount'] );
                        $trnFundRaiserCircleEventSubEvents->setSubEventTargetAmount($data['subEventAmount']);
                        $trnFundRaiserCircleEventSubEvents->setSubEventRemarks($data['subEventRemarks']);
                        $objMstCurrency = $this->getDoctrine()->getRepository(MstCurrency::class)->findOneBy(array('iso3' => $data['subEventCurrency']));
                        $trnFundRaiserCircleEventSubEvents->setMstCurrencySubEvent($objMstCurrency);
                        $trnFundRaiserCircleEventSubEvents->setTimePeriodSupported($data['subEventTimePeriodSupported']);
                        $trnFundRaiserCircleEventSubEvents->setNoOfBeneficiaries($data['subEventNoOfBeneficiaries']);
                        $trnFundRaiserCircleEventSubEvents->setAppUser($appUser);
                        $trnFundRaiserCircleEventSubEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                        $trnFundRaiserCircleEventSubEvents->setCreatedOn(new \DateTime());
                        $trnFundRaiserCircleEventSubEvents->setIsActive(true);
                        $trnFundRaiserCircleEventSubEvents->setTrnFundRaiserCircleEventDetails($trnFundRaiserCircleEventDetails);
                        $entityManager->persist($trnFundRaiserCircleEventSubEvents);
                        $trnFundRaiserCircleEventDetails->addTrnFundRaiserCircleEventSubEvent($trnFundRaiserCircleEventSubEvents);
                        $entityManager->persist($trnFundRaiserCircleEventDetails);
                        $entityManager->flush();
                    }
                    $entityManager->flush();
                    $session->remove('fundRaiserSubEvents'); // once details entered, remove from session

                    if( $nCurrentIndex == count($arrEventProductType) - 1 ) {
                        if($request->get('submission_type') == 'review') {
                            return $this->redirectToRoute('create-an-event-review');
                        } else {
                            return $this->redirectToRoute('create-an-event-thank-you');
                        }
                    }

                    else{
                        $nCurrentIndex = $nCurrentIndex + 1;
                        return $this->redirectToRoute('show-event-product-type-ui', array('currentIndex' => $nCurrentIndex));
                    }
                }
                $arrMstCurrency = $this->getDoctrine()->getRepository(MstCurrency::class)->findAllActive();
                $arrMstBankAccountType = $this->getDoctrine()->getRepository(MstBankAccountType::class)->findAllActive();

                return $this->render('portal/event/create-an-event-funds.html.twig', ['form' => $form->createView
                (), 'arrEventProductType' => $arrEventProductType, 'trnCircle' => $trnCircle, 'currentIndex' =>
                    $nCurrentIndex, 'arrMstCurrency'=> $arrMstCurrency, 'fundRaiserSubEventsCopy' =>
                    $fundRaiserSubEventsCopy, 'fundCircleEventRecurringDetails' => $fundCircleEventRecurringDetails,
                    'trnFundRaiserCircleEventDetails' => $trnFundRaiserCircleEventDetails, 'arrMstBankAccountType' => $arrMstBankAccountType]);
                break;
            }
            default:{
                return $this->redirectToRoute('create-an-event');
                break;
            }
        }
        return $this->redirectToRoute('create-an-event');
    }

    /**
     * @Route("/create-an-event-copy-from-library", name="create-an-event-copy-from-library", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function createEventCopyFromLibrary(Request $request, TokenStorageInterface $tokenStorage,
                                               AppUserRepository $appUserRepository, TrnCircleEventsRepository $trnCircleEventsRepository) :Response
    {
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);
        $em = $this->getDoctrine();
        $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findAll(["isActive" => true]);
        $arrTemp = array();

        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if (strtolower($MstEventProductType) != 'crowdfunding') {
                $arrTemp[] = $MstEventProductType->getId();
            }
        }
        $activatedObjMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);

        $objMstStatus[] =  $activatedObjMstStatus;
        $objMstStatus[] =  $expiredObjMstStatus;

        $arrParameters['copySearch'] = array('includeCrowdfunding' => 'no');

        $arrEventList =  $trnCircleEventsRepository->getAllEventsOfUser($objMstStatus, $this->getParameter('company_id')
            , $appUser, $arrTemp, $arrParameters);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        if($request->isMethod('POST')){
            return $this->redirectToRoute('create-an-event-copy-from-library-review');
        }
        return $this->render('portal/event/create-an-event-copy-from-library.html.twig', [ 'arrEventList' =>
            $arrEventList, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails]);
    }

    /**
     * @Route("/ajax-event-copy-from-library", name="ajax-event-copy-from-library", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function ajaxEventCopyFromLibrary(Request $request, TokenStorageInterface $tokenStorage,
                                               AppUserRepository $appUserRepository, TrnCircleEventsRepository $trnCircleEventsRepository) :Response
    {
        $searchText = $request->get('searchText');
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);
        $em = $this->getDoctrine();

        $arrTemp = array();
        $arrProductType = array();
        $searchArr = array();
        $searchresource = array();

        if(strtolower($searchText) == strtolower('Fundraiser') ) {

            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(['eventProductType' => "Fundraiser"]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                $arrTemp[] = $MstEventProductType->getId();
            }
        } else if(strtolower($searchText) == strtolower('Volunteer (in Time)') ) {
            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(['eventProductType' => "Volunteer (in Time)"]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                $arrTemp[] = $MstEventProductType->getId();
            }
        } else if(strtolower($searchText) == strtolower('Material (in Kind)') ) {
            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(['eventProductType' => "Material (in Kind)"]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                $arrTemp[] = $MstEventProductType->getId();
            }
        } else {
            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findAll(["isActive" => true]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                if (strtolower($MstEventProductType) != 'crowdfunding') {
                    $arrTemp[] = $MstEventProductType->getId();
                }
            }
        }

        // If any resource type matches, filter by that resource or get all resources
        if(count($searchresource) > 0) {
            $arrProductType = $searchresource;
        } else {
            $arrProductType = $arrTemp;
        }

        $activatedObjMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);

        $objMstStatus[] =  $activatedObjMstStatus;
        $objMstStatus[] =  $expiredObjMstStatus;

        if(strtolower($searchText) == 'past' || strtolower($searchText) == 'upcoming' || strtolower($searchText) == 'ongoing') {
            $searchArr['copySearch']['status'] = strtolower($searchText);
        }
        $searchArr['copySearch']['searchText'] = $searchText;
        $searchArr['copySearch']['includeCrowdfunding'] = 'no';

        $arrEventList =  $trnCircleEventsRepository->getAllEventsOfUser($objMstStatus, $this->getParameter('company_id')
            , $appUser, $arrTemp, $searchArr);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);

        $response = $this->render('portal/event/_ajax-event-copy-from-library.html.twig', [
            'arrEventList' =>
                $arrEventList, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);

        return new JsonResponse([ 'html' => $response->getContent(), 'count' => count($arrEventList)]);
    }

    /**
     * @Route("/create-an-event-copy-from-library-review", name="create-an-event-copy-from-library-review", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function createEventCopyFromLibraryReview(Request $request) :Response
    {
        $trnCircleEventId = $request->query->get('id');
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');
        if($request->isMethod('POST')){
            return $this->redirectToRoute('create-an-event-thank-you');
        }
        $arrTrnAreaOfInterests =  $trnCircleEvents->getTrnAreaOfInterests();
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
        $trnVolunterCircleEventDetails = null;
        $trnVolunterCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();

        if (!empty($trnVolunterCircleEventDetailsArray) && !empty($trnVolunterCircleEventDetailsArray[0])) {
            $trnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsArray[0];
        }

        $trnMaterialInKindCircleEventDetails = null;
        $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
        if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
            $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
        }

        $trnFundRaiserCircleEventDetails = null;
        $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
        if(!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])){
            $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
        }
        $trnCircle = $trnCircleEvents->getTrnCircle();
        $url = "http://www.google.com";
        return $this->render('portal/event/create-an-event-copy-from-library-review.html.twig', ['trnCircleEvents' =>
            $trnCircleEvents, 'currentIndex' => 1000, 'arrPrimaryAISecAI' => $arrPrimaryAISecAI,  'arrPrimaryAI' =>
            $arrPrimaryAI, 'trnVolunterCircleEventDetails' => $trnVolunterCircleEventDetails, 'url' => $url,
            'trnMaterialInKindCircleEventDetails'=> $trnMaterialInKindCircleEventDetails,
            'trnFundRaiserCircleEventDetails' => $trnFundRaiserCircleEventDetails, 'trnCircle' => $trnCircle]);
    }

    /**
     * @Route("/create-an-event-copied", name="create-an-event-copied", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function createEventCopied(Request $request, SessionInterface $session) :Response
    {
        $trnCircleEventId = $request->query->get('id');
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $session->set('copyEventId', $trnCircleEventId);
        return $this->redirectToRoute('create-event-copying');
    }

    /**
     * @Route("/create-event-copying", name="create-event-copying", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param SessionInterface $session
     * @param FileUploaderHelper $fileUploaderHelper
     * @param Filesystem $filesystem
     * @return Response
     * @throws \ReflectionException
     */
    public function createEventCopying(Request $request, TokenStorageInterface $tokenStorage, AppUserRepository
    $appUserRepository, SessionInterface $session, FileUploaderHelper $fileUploaderHelper, Filesystem $filesystem) :Response
    {
        $trnCircleEventId = $session->get('copyEventId', array());
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        $newCircleEvents = new TrnCircleEvents();
        $reflectionClass = new \ReflectionClass($trnCircleEvents);
        $methods = array_filter($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC), function(\ReflectionMethod $method){
            return strpos($method->name, "get") === 0;
        });
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);
        foreach ($methods as $method) {
            $methodName = $method->name;
            $setter = str_replace("get", "set", $methodName);
            $value = $trnCircleEvents->$methodName();

            switch ($setter) {
                case 'setUserIpAddress': {
                    $newCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
                    break;
                }
                case 'setCreatedOn': {
                    $newCircleEvents->setCreatedOn(new \DateTime());
                    break;
                }
                case 'setReadCount': {
                    $newCircleEvents->setReadCount(0);
                    break;
                }
                case 'setLikeCount': {
                    $newCircleEvents->setLikeCount(0);
                    break;
                }
                case 'setShareCount': {
                    $newCircleEvents->setShareCount(0);
                    break;
                }
                case 'setMstStatus': {
                    $newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    =>  'Pending Activation']));
                    break;
                }
                case 'setAppUser': {
                    $newCircleEvents->setAppUser($appUser);
                    break;
                }
                case 'setTrnCircleEventUploadedDocuments': {
                    foreach ($value as $trnCircleEventUploadedDocument){
                        $newCircleEvents->removeTrnCircleEventUploadedDocument($trnCircleEventUploadedDocument);
                        $newTrnCircleEventUploadedDocument = clone $trnCircleEventUploadedDocument;
                        $newCircleEvents->addTrnCircleEventUploadedDocument($newTrnCircleEventUploadedDocument);
                    }
                    break;
                }
                case 'setTrnVolunteerCircleParticipationDetails':
                case 'setTrnCircleEventComments':
                case 'setTrnCircleEventLeads':
                case 'setLocationLatitude':
                case 'setLocationLongitude':
                case 'setId':
                case 'setAddress':
                case 'setGeoCode':
                case 'setIsDistributedEvent':
                case 'setParentTrnCircleEvents':
                case 'setChildTrnCircleEvents':
                case 'setTrnCrowdFundEventsEventLink':
                case 'setTrnCircleEventGoodnessTimelines':
                case 'setTrnCircleEventBroadCastDetails':
                case 'setTrnCircleEventInvitations':
                case 'setTrnCircleEventRecurringDetails':
                case 'setMstEventProductType':
                case 'setTrnMaterialReceivedAtCollectionCentres':
                case 'setTrnVolunterCircleEventVolunterDetails':
                case 'setTrnCircleEventRequestToParticipates':
                case 'setTrnVolunterCircleEventDetails':
                case 'setTrnFundRaiserCircleEventDetails':
                case 'setTrnMaterialInKindCircleEventDetails':
                case 'setTrnOrders':
                case 'setTrnOrderDetails':
                case 'setTrnCircleEventDeactivatingReasons':
                case 'setTrnCircleEventReminders':
                case 'setIsTargetAchieved':
                case 'setIsTarsetAchieved':
                case 'setTrnCircleEventsVisitors':
                case 'setTrnCrowdFundEvents' :{
                    break;
                }
                case 'setTrnProductMedia': {
                    foreach ($value as $trnProductMedia) {
                        $newCircleEvents->removeTrnProductMedium($trnProductMedia);
                        $newTrnProductMedia = clone $trnProductMedia;

                        // change the name of media file
                        /*if($newTrnProductMedia->getMediaType() == 'image') {

                            // change the name appending some uuid, copy files with new name
                            $oldFileName = $newTrnProductMedia->getMediaFileName();
                            $extArr = explode('.',$oldFileName);
                            $extension = $extArr[(count($extArr)-1)];
                            $oldMediaName = $newTrnProductMedia->getMediaName();
                            $newFileName = $oldMediaName.'copy'.Uuid::uuid4()->toString().".$extension";

                            $newTrnProductMedia->setMediaFileName($newFileName);
                        }*/
                        $newCircleEvents->addTrnProductMedium($newTrnProductMedia);
                    }
                    break;
                }
                case 'setTrnAreaOfInterests': {
                    foreach ($value as $trnAreaOfInterests) {
                        $newCircleEvents->removeTrnAreaOfInterest($trnAreaOfInterests);
                        $newTrnAreaOfInterests = clone $trnAreaOfInterests;
                        $newCircleEvents->addTrnAreaOfInterest($newTrnAreaOfInterests);
                    }
                    break;
                }
                case 'setBackgroundImagePath':
                case 'setProfileImage': {
                    $value = str_ireplace('files/','',$value);
                    $newCircleEvents->$setter($value);
                    break;
                }
                default: {
                    $newCircleEvents->$setter($value);
                    break;
                }
            }
        }
        $form = $this->createForm(TrnCircleEventsPortalType::class, $newCircleEvents, array('mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated'])));
        $form->handleRequest($request);
        $arrEventProductTypeId = $arrEventProductType = array();
        foreach ($trnCircleEvents->getMstEventProductType() as $MstEventProductType) {
            $newCircleEvents->addMstEventProductType($MstEventProductType);
            $arrEventProductType[] = $MstEventProductType->getEventProductType();
            $arrEventProductTypeId[] = $MstEventProductType->getId();
        }
        $arrTrnAreaOfInterests =  $newCircleEvents->getTrnAreaOfInterests();
        $arrPrimaryAISelected = $arrSecondaryAISelected = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            $arrPrimaryAISelected[] = $TrnAreaOfInterest->getAreaInterestPrimary()->getId();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrSecondaryAISelected[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getId();
            }
        }
        $arrImages = array();
        if (!empty($newCircleEvents->getProfileImage()) && $newCircleEvents->getProfileImage() != 'files/') {
            $arrImages['profile'] = $newCircleEvents->getProfileImage();
        }
        if (!empty($newCircleEvents->getBackgroundImagePath()) && $newCircleEvents->getBackgroundImagePath() != 'files/') {
            $arrImages['backgroundImage'] = $newCircleEvents->getBackgroundImagePath();
        }

        foreach ($newCircleEvents->getTrnProductMedia() as $trnProductMedia) {
            if (  strtolower($trnProductMedia->getMediaType()) == 'image' && $trnProductMedia->getMediaFileName()) {
                $arrImages['imageGallery'][] =  $trnProductMedia->getuploadedFilePath();
            }
        }
        if($form->isSubmitted() && $form->isValid()){

            // used to load new files
            $file = $request->files;
            $entityManager = $this->getDoctrine()->getManager();
            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $arrSecondaryAI = explode(',', $secondaryAreaOfInterest);
            $objPrimaryAreaInterest = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find
            ($primaryAreaOfInterest);
            $objTrnAreaOfInterest = new TrnAreaOfInterest();
            $objTrnAreaOfInterest->setAreaInterestPrimary($objPrimaryAreaInterest);
            $objSecondaryAI = null;
            $preloadedImage = $request->get('preloadedGalImg');

            // remove old product type of event and add newly selected as in copy, it might get changed
            foreach ($newCircleEvents->getMstEventProductType() as $MstEventProductType) {
                $newCircleEvents->removeMstEventProductType($MstEventProductType);
            }

            $getProductType = $request->get('trn_circle_events_portal')['mstEventProductType'];
            $arrEventProductTypeId = $arrEventProductType = array();
            foreach ($getProductType as $productTypeId) {
                $MstEventProductType = $this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["id" => $productTypeId]);
                $newCircleEvents->addMstEventProductType($MstEventProductType);
                $arrEventProductType[] = $MstEventProductType->getEventProductType();
                $arrEventProductTypeId[] = $MstEventProductType->getId();
            }

            // get other approved/expired fund event count of user.
            $userFundsEvents = 0;
            $isFundEvent = false;
            foreach($trnCircleEvents->getMstEventProductType() as $evePrdType) {

                if($evePrdType->getEventProductType() == 'Fundraiser') {
                    $isFundEvent = true;
                    $userFundsEvents = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->getFundsEventByUser($userId);
                }
            }
            if($userFundsEvents > 0 || $isFundEvent == false) {
                // if any event there of user, set status to activated
                $newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Activated']));
            } else {
                // else set status to pending activation
                $newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                =>  'Pending Activation']));
            }
            /*$newCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
            =>  'Activated']));*/
            $newCircleEvents->setCreatedOn(new \DateTime());
            $newCircleEvents->setAppUser($appUser);
            $newCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $newCircleEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')) );
            $newCircleEvents->setIsActive(0);
            //dd($newCircleEvents->getTrnVolunterCircleEventDetails()[0]->getTrnVolunterCircleEventSubEvents());
            $entityManager->persist($newCircleEvents);
            $entityManager->flush();

            // removeAreaInterestSecondary

            $existingSecondaryAI =  $newCircleEvents->getTrnAreaOfInterests();
            $arrPrimaryAISelected = $arrSecondaryAISelected = array();

            foreach ($existingSecondaryAI as $TrnAreaOfInterest) {

                $this->getDoctrine()->getManager()->remove($TrnAreaOfInterest);
                $this->getDoctrine()->getManager()->flush();
            }

            foreach ($arrSecondaryAI as  $key => $nSecAI) {
                $objSecondaryAI = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI);
                $objTrnAreaOfInterest->addAreaInterestSecondary($objSecondaryAI);
            }
            $newCircleEvents->addTrnAreaOfInterest($objTrnAreaOfInterest);

            if (!empty($file)) {
                $objProfileImageData = $request->files->get('profileImage');
                $objBackGroundImage = $request->files->get('backGroundImage');
                $arrImageGallery = $request->files->get('imageGallery');

                if (!empty($objProfileImageData) && !empty($objProfileImageData[0])) {
                    $objProfileImageData = $objProfileImageData[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objProfileImageData,
                        $trnCircleEvents->getName().
                        ' profileImage'.Uuid::uuid4()->toString(), null);
                    $newCircleEvents->setProfileImage($newFilename);
                }
                if (!empty($objBackGroundImage) && !empty($objBackGroundImage[0])) {
                    $objBackGroundImage = $objBackGroundImage[0];
                    $newFilename = $fileUploaderHelper->uploadPublicFile($objBackGroundImage,
                        $trnCircleEvents->getName().
                        ' backGroundImage'.Uuid::uuid4()->toString(), null);
                    $newCircleEvents->setBackgroundImagePath($newFilename);
                }
                $entityManager->persist($trnCircleEvents);

                // Remove old media file if any removed from box
                if(!empty($request->get('preloadedGalImg'))) {

                    foreach ($newCircleEvents->getTrnProductMedia() as $trnProductMedia) {

                        if($trnProductMedia->getMediaType() == 'image') {
                            if(!in_array('/images/'.$trnProductMedia->getuploadedFilePath(), $preloadedImage)) {
                                $newCircleEvents->removeTrnProductMedium($trnProductMedia);
                            } else {
                                // change the name appending some uuid, copy files with new name
                                $oldFileName = $trnProductMedia->getMediaFileName();
                                $oldFilePath = $this->getParameter('public_file_folder').'/'.$oldFileName;
                                $extArr = explode('.',$oldFileName);
                                $extension = $extArr[(count($extArr)-1)];
                                $oldMediaName = $trnProductMedia->getMediaName();
                                $newFileName = $oldMediaName.'copy'.Uuid::uuid4()->toString().".$extension";
                                $newFilePath = $this->getParameter('public_file_folder').'/'.$newFileName;

                                $filesystem->copy($oldFilePath, $newFilePath);
                                $trnProductMedia->setMediaFileName($newFileName);
                            }
                        }
                    }
                } else {
                    // remove all old image productMedia
                    foreach ($newCircleEvents->getTrnProductMedia() as $trnProductMedia) {
                        if($trnProductMedia->getMediaType() == 'image') {
                            $newCircleEvents->removeTrnProductMedium($trnProductMedia);
                        }
                    }
                }

                if (!empty($arrImageGallery)) {
                    foreach ($arrImageGallery as $key => $objImageGallery) {
                        if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                            $objImageGallery = $objImageGallery[0];
                            $trnProductMedia = new TrnProductMedia();
                            $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                            $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                            $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                            $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                            $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                            $trnProductMedia->setMediaType('image');
                            $trnProductMedia->setMediaName('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaAltText('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setMediaTitle('GalleryImage'.$trnCircleEvents->getId().$key);
                            $trnProductMedia->setIsActive(1);
                            $newFilename = $fileUploaderHelper->uploadPublicFile($objImageGallery, 'GalleryImage'
                                .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                            $trnProductMedia->setMediaFileName($newFilename);
                            $trnProductMedia->setUploadedFilePath($this->getParameter('public_file_folder'));
                            $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                            $trnProductMedia->setCreatedOn(new DateTime());
                            $entityManager->persist($trnProductMedia);
                            $newCircleEvents->addTrnProductMedium($trnProductMedia);
                        }
                    }
                }
                $entityManager->flush();
            }
            $session->set('trnCircleEventId', $newCircleEvents->getId());
            //$session->remove('copyEventId');
            return $this->redirectToRoute('show-event-product-type-ui');
        }

        return $this->render('portal/event/create-an-event-copied.html.twig', [
            'data' => $newCircleEvents, 'form' => $form->createView(), 'arrEventProductType' => $arrEventProductType,
            'arrPrimaryAISelected' => $arrPrimaryAISelected, 'arrSecondaryAISelected' => $arrSecondaryAISelected,
            'arrEventProductTypeId' => $arrEventProductTypeId, 'arrImages' => $arrImages
        ]);
    }

    /**
     * @Route("/create-an-event-review", name="create-an-event-review", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function createEventReview(Request $request, SessionInterface $session) :Response
    {
        $session->remove('copyEventId');
        $session->remove('editEvent');

        $trnCircleEventId = $session->get('trnCircleEventId', array());
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-an-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');
        if($request->isMethod('POST')){
            return $this->redirectToRoute('create-an-event-thank-you');
        }
        $arrTrnAreaOfInterests =  $trnCircleEvents->getTrnAreaOfInterests();
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
        $trnVolunterCircleEventDetails = null;
        $trnVolunterCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();

        if (!empty($trnVolunterCircleEventDetailsArray) && !empty($trnVolunterCircleEventDetailsArray[0])) {
            $trnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsArray[0];
        }
        $trnVolunterCircleEventDetails_recurringDetails = array();
        $recurringDetailsArray = $trnCircleEvents->getTrnCircleEventRecurringDetails();
        if(!empty($recurringDetailsArray) && !empty($recurringDetailsArray[0])){
            $trnVolunterCircleEventDetails_recurringDetails = $recurringDetailsArray[0];
        }

        $trnMaterialInKindCircleEventDetails = null;
        $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
        if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
            $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
        }

        $trnFundRaiserCircleEventDetails = null;
        $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
        if(!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])){
            $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
        }
        $trnCircle = $trnCircleEvents->getTrnCircle();
        $url = "http://www.google.com";
        return $this->render('portal/event/create-an-event-review.html.twig', [ 'trnCircleEvents' =>
            $trnCircleEvents, 'currentIndex' => 1000, 'arrPrimaryAISecAI' => $arrPrimaryAISecAI,  'arrPrimaryAI' =>
            $arrPrimaryAI, 'trnVolunterCircleEventDetails' => $trnVolunterCircleEventDetails, 'url' => $url,
            'trnMaterialInKindCircleEventDetails'=> $trnMaterialInKindCircleEventDetails,
            'trnFundRaiserCircleEventDetails' => $trnFundRaiserCircleEventDetails, 'trnCircle' => $trnCircle,
            'trnVolunterCircleEventDetails_recurringDetails' => $trnVolunterCircleEventDetails_recurringDetails]);
    }

    /**
     * @Route("/create-an-event-thank-you", name="create-an-event-thank-you", methods={"GET", "POST"})
     * @param Request $request
     * @param UserInterface $user
     * @param AppUserRepository $appUserRepository
     * @param SessionInterface $session
     * @param Mailer $mailer
     * @param NotificationService $notificationService
     * @return Response
     */
    public function createEventThankYou(Request $request, UserInterface $user, AppUserRepository $appUserRepository,
                                        SessionInterface $session, Mailer $mailer, NotificationService
                                        $notificationService, ProjectService $projectService) :Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        $trnCircleEvents->setIsActive(1);
        $entityManager->persist($trnCircleEvents);

        $objStatusPending = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
        =>  'Pending Activation']);
        $objStatusActivated = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
        =>  'Activated']);

        $objCollectionCenters = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)->findBy(
            ['trnCircleEvent' => $trnCircleEvents, 'mstStatus' => $objStatusPending]);

        foreach ($objCollectionCenters as $objCenter) {
            $objCenter->setIsActive(1);
            $objCenter->setMstStatus($objStatusActivated);
            $entityManager->persist($objCenter);
        }
        $entityManager->flush();

        $eventName =  $trnCircleEvents->getName();

        $session->remove('trnCircleEventId');

        //Event Created Creator
        $notificationService->setAppUser($trnCircleEvents->getAppUser());
        $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
        $notificationService->setTrnCircleEvents($trnCircleEvents);
        $notificationService->doProcess('Event Created Creator');
        $notificationService->doGCProcess('Event Created GC');
        $projectService->sendEventNotificationToAllUser($trnCircleEvents);
        // send notification to User for event creation

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
/*
        $mailer->sendSuccessfulEventCreationUserMail($userEmail, $userName, $eventName);
*/
        // send notification to GC for event creation

//      $gcEmail = 'contact@givingcircle.in';
//      $gcEmail = $this->getParameter('gc_email');
/*
        if(isset($gcEmail)) {
            $mailer->sendSuccessfulEventCreationGCMail($gcEmail, $userName, $eventName);
        }*/


        return $this->render('portal/event/create-an-event-thank-you.html.twig', []);
    }

    /**
     * @Route("/create-event-steps", name="create-event-steps", methods={"GET", "POST"})
     * @param null $currentIndex
     * @param SessionInterface $session
     * @return Response
     */
    public function createEventSteps($currentIndex = null, SessionInterface $session) : Response
    {
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        if (empty($trnCircleEventId)){
            return $this->render('portal/event/create-event-steps.html.twig', [ 'currentIndex' => $currentIndex]);
        }
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents)){
            return $this->render('portal/event/create-event-steps.html.twig', [ 'currentIndex' => $currentIndex]);
        }

        foreach ($trnCircleEvents->getMstEventProductType() as $mstEventProductType){
            $arrEventProductType[] = str_ireplace(array("(", ")", " "), array("","",""), strtolower($mstEventProductType->getEventProductType()));
        }

        if (empty($arrEventProductType)) {
            return $this->render('portal/event/create-event-steps.html.twig', [ 'currentIndex' => $currentIndex]);
        }
        $strEventProductType = "";
        if (!empty($arrEventProductType[$currentIndex]))
            $strEventProductType =  $arrEventProductType[$currentIndex];
        return $this->render('portal/event/create-event-steps.html.twig', [ 'currentIndex' => $currentIndex,
            'strEventProductType' => $strEventProductType, 'arrEventProductType' => $arrEventProductType]);
    }

    /**
     * @Route("/get-event-skill-added-ui", name="get-event-skill-added-ui", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function getSkillAddedUI(Request $request, SessionInterface $session)
    {
        $trnCircleEventCopyId = $session->get('copyEventId', array());

        if($trnCircleEventCopyId == null && $session->has('trnCircleEventId')) {
            $trnCircleEventCopyId = $session->get('trnCircleEventId', array());
        }

        if (!empty($trnCircleEventCopyId)){
            $trnCircleEventCopy = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventCopyId);
        }
        $arrCopySkillData = array();
        if (!empty($trnCircleEventCopy)) {

            foreach ($trnCircleEventCopy->getTrnVolunterCircleEventDetails() as $trnVolunterCircleEventDetailsCopy) {

                foreach ($trnVolunterCircleEventDetailsCopy->getTrnVolunterCircleEventSubEvents() as $TrnVolunterCircleEventSubEventsCopy ) {

                    $name = $TrnVolunterCircleEventSubEventsCopy->getSubEventName();
                    $nameslug = str_replace(' ', '_', strtolower($name));

                    $arrCopySkillData[$nameslug]['id'] = $TrnVolunterCircleEventSubEventsCopy->getId();
                    $arrCopySkillData[$nameslug]['subEventName'] = $name;
                    $arrCopySkillData[$nameslug]['hours'] = $TrnVolunterCircleEventSubEventsCopy->getNumberOfHours();
                    $arrCopySkillData[$nameslug]['subEventReccuringBy'] = $TrnVolunterCircleEventSubEventsCopy->getSubEventReccuringBy();
                }
            }
        }

        $arrSkillAdded = $request->get('strSkillAdded');
        //$arrMstSkillSet = $this->getDoctrine()->getRepository(MstSkillSet::class)->findBy(["id" =>  $arrSkillAdded]);

        $arrHours = range(1, 12);
        return $this->render('portal/event/_ajax-get-event-skill-added-ui.html.twig', [ /*'arrMstSkillSet' =>
            $arrMstSkillSet,*/ 'arrSkillAdded' => $arrSkillAdded,'arrHours' => $arrHours, 'arrCopySkillData' => $arrCopySkillData]);
    }

    /**
     * @Route("/get-map-ui", name="get-map-ui", methods={"GET", "POST"})
     * @param null $onSiteAddresses
     * @return Response
     */
    public function getMapUI($onSiteAddresses = null){
        return $this->render('portal/event/map.html.twig', [
            'onSiteAddresses' => $onSiteAddresses
        ]);
    }

    /**
     * @Route("/get-collection-center-from-master", name="get-collection-center-from-master", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function getCollectionCenterFromMaster(Request $request, SessionInterface $session, TokenStorageInterface $tokenStorage) {

        $collectionCenter = array();
        $circleId = $request->get('circleId');
        if($session->has('collectionCenter')) {
            $collectionCenter = $session->get('collectionCenter', array());
        } else if($request->get('collectionCenter') != null) {
            $collectionCenter = $request->get('collectionCenter');
        }

        /*$arrTrnCollectionCentreDetails = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)
            ->findBy(['isActive' => 1,
                'mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated']),
                'trnCircle' => $circleId]);*/

        $userId = $tokenStorage->getToken()->getUser();

        $arrTrnCollectionCentreDetails = $this->getDoctrine()
            ->getRepository(TrnCollectionCentreDetails::class)
            ->getCollectionCenterMaster($circleId, $userId);

        $trnCircleEventCopyId = $session->get('copyEventId', array());
        $trnCircleEventCopy = null;
        if (!empty($trnCircleEventCopyId)){
            $trnCircleEventCopy = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventCopyId);
        }
        if (!empty($trnCircleEventCopy)) {
            foreach ($trnCircleEventCopy->getTrnMaterialInKindCircleEventDetails() as $trnMaterialInKindCircleEventDetailsCopy) {
                foreach ($trnMaterialInKindCircleEventDetailsCopy->getTrnMaterialInKindCircleEventCollectionCentres()
                         as $TrnMaterialInKindCircleEventCollectionCentres) {
                    $collectionCenter[] = $TrnMaterialInKindCircleEventCollectionCentres->getTrnCollectionCentreDetails()->getId();
                }
            }
        }
        return $this->render('portal/collection-center/_ajax-collection-center-from-master.html.twig',
            [ 'arrTrnCollectionCentreDetails' => $arrTrnCollectionCentreDetails, 'collectionCenter' =>
                $collectionCenter]);
    }

    /**
     * @Route("/add-selected-collection-center-to-ui", name="add-selected-collection-center-to-ui", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function addSelectedCollectionCenterToUI(Request $request, SessionInterface $session) {
        $collectionCenter = $request->get('collectionCenter');
        $session->set('collectionCenter', $collectionCenter);

        $trnCircleEventCopyId = $session->get('copyEventId', array());
        $trnCircleEventEditId = $session->get('trnCircleEventId', array());
        $editEvent = $session->get('editEvent', false);
        $trnCircleEventCopy = null;
        $trnCircleEventEdit = null;
        $arrTrnCollectionCentreDetails = null;

        if($editEvent == true && !empty($trnCircleEventEditId) ) {
            $arrTrnCollectionCentreDetails = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)
                ->findBy(['id' => $collectionCenter, 'trnCircleEvent' => $trnCircleEventEditId]);
        }

        if(empty($arrTrnCollectionCentreDetails)) {
            $arrTrnCollectionCentreDetails = $this->getDoctrine()->getRepository(TrnCollectionCentreDetails::class)
                ->findBy(['id' => $collectionCenter]);
        }

        $arrCollectionCenterDaysOfWeek = array();
        foreach ($arrTrnCollectionCentreDetails as $TrnCollectionCentreDetails ) {
            $arrMstDaysOfWeek = $TrnCollectionCentreDetails->getMstDaysOfWeek();
            foreach ($arrMstDaysOfWeek as $mstDaysOfWeek ) {
                $arrCollectionCenterDaysOfWeek[$TrnCollectionCentreDetails->getId()][] = $mstDaysOfWeek->getDayOfWeek();
            }
        }

        if (!empty($trnCircleEventCopyId)){
            $trnCircleEventCopy = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($trnCircleEventCopyId);
        }
        $collectionCenterDate = array();
        if (!empty($trnCircleEventCopy)) {
            foreach ($trnCircleEventCopy->getTrnMaterialInKindCircleEventDetails() as $trnMaterialInKindCircleEventDetailsCopy) {
                foreach ($trnMaterialInKindCircleEventDetailsCopy->getTrnMaterialInKindCircleEventCollectionCentres()
                         as $TrnMaterialInKindCircleEventCollectionCentres) {
                    if (!empty($TrnMaterialInKindCircleEventCollectionCentres->getFromDate()) &&
                        $TrnMaterialInKindCircleEventCollectionCentres->getToDate()) {
                        $collectionCenterDate[$TrnMaterialInKindCircleEventCollectionCentres->getTrnCollectionCentreDetails()
                            ->getId()] = array('formDate' => $TrnMaterialInKindCircleEventCollectionCentres->getFromDate()->format('d-m-Y'),
                            'toDate' => $TrnMaterialInKindCircleEventCollectionCentres->getToDate()->format('d-m-Y'));
                    }
                }
            }
        }

        if($editEvent == true) {
            if (!empty($trnCircleEventEditId)){
                $trnCircleEventEdit = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($trnCircleEventEditId);
            }
        }
        if (!empty($trnCircleEventEdit)) {
            foreach ($trnCircleEventEdit->getTrnMaterialInKindCircleEventDetails() as $trnMaterialInKindCircleEventDetailsCopy) {
                foreach ($trnMaterialInKindCircleEventDetailsCopy->getTrnMaterialInKindCircleEventCollectionCentres()
                         as $TrnMaterialInKindCircleEventCollectionCentres) {
                    if (!empty($TrnMaterialInKindCircleEventCollectionCentres->getFromDate()) &&
                        $TrnMaterialInKindCircleEventCollectionCentres->getToDate()) {
                        $collectionCenterDate[$TrnMaterialInKindCircleEventCollectionCentres->getTrnCollectionCentreDetails()
                            ->getId()] = array('formDate' => $TrnMaterialInKindCircleEventCollectionCentres->getFromDate()->format('d-m-Y'),
                            'toDate' => $TrnMaterialInKindCircleEventCollectionCentres->getToDate()->format('d-m-Y'));
                    }
                }
            }
        }

        $arrHours = range(1, 24);
        $arrMstDaysOfWeek = $this->getDoctrine()->getRepository(MstDaysOfWeek::class)->findAll();
        return $this->render('portal/collection-center/_ajax-selected-collection-center-to-ui.html.twig', [ 'arrTrnCollectionCentreDetails' =>
            $arrTrnCollectionCentreDetails, 'arrMstDaysOfWeek' => $arrMstDaysOfWeek, 'arrCollectionCenterDaysOfWeek'
        => $arrCollectionCenterDaysOfWeek, 'collectionCenterDate' => $collectionCenterDate, 'arrHours' => $arrHours ]);
    }

    /**
     * @Route("/remove-collection-center-from-section", name="remove-collection-center-from-section", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function removeCollectionCenterFromSession(Request $request, SessionInterface $session) {
        $collectionCenterId = $request->get('collectionCenterId');
        $collectionCenter = $session->get('collectionCenter', array());
        if (!empty($collectionCenter) && array_search($collectionCenterId, $collectionCenter) !== false) {
            $key = array_search($collectionCenterId, $collectionCenter);
            unset($collectionCenter[$key]);
        }
        $session->set('collectionCenter', $collectionCenter);
        return $this->json(['message' => 'Successfully updated']);
    }

    /**
     * @Route("/add-fund-raiser-sub-events-to-session", name="add-fund-raiser-sub-events-to-session", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function addFundRaiserSubEventsToSession(Request $request, SessionInterface $session) {
        $subEventTimePeriodSupported = $request->get('subEventTimePeriodSupported');
        $subEventNoOfBeneficiaries 	 = $request->get('subEventNoOfBeneficiaries');
        $subEventCurrency 			 = $request->get('subEventCurrency');
        $subEventAmount 			 = $request->get('subEventAmount');
        $subEventRemarks 			 = $request->get('subEventRemarks');

        $fundRaiserSubEvents = $session->get('fundRaiserSubEvents', array());
        $fundRaiserSubEvents[] = array( 'subEventTimePeriodSupported' => $subEventTimePeriodSupported,
            'subEventNoOfBeneficiaries' => $subEventNoOfBeneficiaries, 'subEventCurrency' => $subEventCurrency,
            'subEventAmount' => $subEventAmount, 'subEventRemarks' => $subEventRemarks);
        $session->set('fundRaiserSubEvents', $fundRaiserSubEvents);
        return $this->render('portal/event/_ajax-get-fund-raise-sub-events.html.twig', [ 'fundRaiserSubEvents' =>
            $fundRaiserSubEvents]);
    }

    /**
     * @Route("/show-fund-raiser-sub-events", name="show-fund-raiser-sub-events", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function showFundRaiserSubEvents(Request $request, SessionInterface $session) {
        $fundRaiserSubEvents = array();
        $session->remove('fundRaiserSubEvents');

        if($session->has('copyEventId')) {
            $trnCircleEventCopyId = $session->get('copyEventId', array());
        } else {
            // added for edit mode
            $trnCircleEventCopyId = $session->get('trnCircleEventId', array());
        }

        $trnCircleEventCopy = null;
        if (!empty($trnCircleEventCopyId)){
            $trnCircleEventCopy = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventCopyId);
        }
        $fundRaiserSubEvents = $fundCircleEventRecurringDetails = array();
        if (!empty($trnCircleEventCopy)) {
            foreach ($trnCircleEventCopy->getTrnFundRaiserCircleEventDetails() as $trnFundRaiserCircleEventDetailsCopy) {
                foreach ($trnFundRaiserCircleEventDetailsCopy->getTrnFundRaiserCircleEventSubEvents() as
                $TrnFundRaiserCircleEventSubEvents) {
                    $tempData = array(
                        'subEventTimePeriodSupported' => $TrnFundRaiserCircleEventSubEvents->getTimePeriodSupported(),
                        'subEventNoOfBeneficiaries' => $TrnFundRaiserCircleEventSubEvents->getNoOfBeneficiaries(),
//                        'subEventCurrency' => $TrnFundRaiserCircleEventSubEvents->getMstCurrencySubEvent()->getCurrency(),
                        'subEventCurrency' => $TrnFundRaiserCircleEventSubEvents->getMstCurrencySubEvent()->getIso3(),
                        'subEventAmount' => $TrnFundRaiserCircleEventSubEvents->getSubEventTargetAmount(),
                        'subEventRemarks' => $TrnFundRaiserCircleEventSubEvents->getSubEventRemarks());
                    $fundRaiserSubEvents[] = $tempData;
                }
            }
        }
        $session->set('fundRaiserSubEvents', $fundRaiserSubEvents);
        return $this->render('portal/event/_ajax-get-fund-raise-sub-events.html.twig', [ 'fundRaiserSubEvents' =>
            $fundRaiserSubEvents]);
    }

    /**
     * @Route("/remove-fund-raiser-sub-events-from-session", name="remove-fund-raiser-sub-events-from-session", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function removeFundRaiserSubEventsFromSession(Request $request, SessionInterface $session) {
        $keyToRemove = $request->get('key');
        $fundRaiserSubEvents = $session->get('fundRaiserSubEvents', array());
        if (!empty($fundRaiserSubEvents) && !empty($fundRaiserSubEvents[$keyToRemove])){
            unset($fundRaiserSubEvents[$keyToRemove]);
        }
        $session->set('fundRaiserSubEvents', $fundRaiserSubEvents);
        return $this->json(['message' => 'Successfully updated']);
    }

    /**
     * @Route("/remove_event_image", name="remove_event_image", methods={"GET","POST"})
     * @param Request $request
     * FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function removeEventImage(Request $request, FileUploaderHelper $fileUploaderHelper): Response
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

    /**
     * @Route("/is_unique_event_name", name="is_unique_event_name", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function isUniqueEventName(Request $request): Response
    {
        $result = true;
        $eventname = $request->get('eventname');
        $eventList = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->findBy(['name' => $eventname]);

        if($eventList != null) {
            // not unique event name
            $result = false;
        }
        return $this->json($result);
    }
}