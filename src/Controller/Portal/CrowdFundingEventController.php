<?php

namespace App\Controller\Portal;


use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnProductMedia;
use App\Entity\Transaction\TrnCrowdFundEventDistributedDetails;
use App\Entity\Transaction\TrnCrowdFundEventDocuments;
use App\Form\Transaction\TrnCircleEventsCrowdFundEventPortalType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\CrowdfundingEventService;
use App\Service\FileUploaderHelper;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use DateTime;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\CommonHelper;
use Ramsey\Uuid\Uuid;

/**
 * Class CrowdFundingEventController
 * @IsGranted("ROLE_APP_USER")
 */
class CrowdFundingEventController extends AbstractController
{

    /**
     * @Route("/create-crowdfunding-event", name="create-crowdfunding-event", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param MyAccountService $myAccountService
     * @param CrowdfundingEventService $crowdfundingEventService
     * @return Response
     */
    public function createCrowdfundingEvent(Request $request, SessionInterface $session,
                                            TokenStorageInterface $tokenStorage, AppUserRepository
                                            $appUserRepository, MyAccountService $myAccountService,
                                            CrowdfundingEventService $crowdfundingEventService): Response
    {
        $trnCircleEvents = new TrnCircleEvents();
        $form = $this->createForm(TrnCircleEventsCrowdFundEventPortalType::class, $trnCircleEvents, array('mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated'])));
        $form->handleRequest($request);
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);
        //$arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle());
        if ($request->isMethod('POST')) {

            $companyId = $this->getParameter('company_id');

            $file = array();
            $youTubeArr = array();
            $uploadedDocuments = array();
            $reportingArr = $request->get('reporting');
            if(isset($reportingArr['image']) && $reportingArr['image'] == 'on') {
                $file = $request->files;
            }
            if(isset($reportingArr['video']) && $reportingArr['video'] == 'on') {
                $youTubeArr = $request->get('youTube');
            }

            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $documentDescription = $request->get('documentDescription');
            $arrSecondaryAI = explode(',', $secondaryAreaOfInterest);

            $entityManager = $this->getDoctrine()->getManager();
            $crowdfundingEventService->addEventDetails($trnCircleEvents, $primaryAreaOfInterest, $appUser, $arrSecondaryAI, $companyId);

            /*$objMstJoinBy = $this->getDoctrine()->getRepository(MstJoinBy::class)->findOneBy(["joinBy" =>  'Closed']);
            $entityManager = $this->getDoctrine()->getManager();

            $objPrimaryAreaInterest = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find
            ($primaryAreaOfInterest);
            $objTrnAreaOfInterest = new TrnAreaOfInterest();
            $objTrnAreaOfInterest->setAreaInterestPrimary($objPrimaryAreaInterest);
            $objSecondaryAI = null;
            $MstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true,
                'eventProductType' => 'Crowdfunding']);*/
            /*$trnCircleEvents->addMstEventProductType($MstEventProductTypeObj);
            $trnCircleEvents->setMstJoinBy($objMstJoinBy);
            $trnCircleEvents->setIsCrowdFunding(1);
            $trnCircleEvents->setMstCity($trnCircleEvents->getTrnCircle()->getMstCity());
            $trnCircleEvents->setMstState($trnCircleEvents->getTrnCircle()->getMstState());
            $trnCircleEvents->setMstCountry($trnCircleEvents->getTrnCircle()->getMstCountry());
            $trnCircleEvents->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
            =>  'Pending Activation']));
            $trnCircleEvents->setCreatedOn(new \DateTime());
            $trnCircleEvents->setAppUser($appUser);
            $trnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnCircleEvents->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')) );
            $trnCircleEvents->setIsActive(0);
            $entityManager->persist($trnCircleEvents);
            $entityManager->flush();*/
            /*foreach ($arrSecondaryAI as  $key => $nSecAI) {
                $objSecondaryAI = $this->getDoctrine()->getRepository(MstAreaInterest::class)->find($nSecAI);
                $objTrnAreaOfInterest->addAreaInterestSecondary($objSecondaryAI);
            }
            $trnCircleEvents->addTrnAreaOfInterest($objTrnAreaOfInterest);*/

            $crowdfundingEventService->addCrowdfundEventDetails($trnCircleEvents, $session);

            /*
             * Remove any Previous Data If Any
             * */
            /*$trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
            if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
                $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];

                $arrTrnCrowdFundEventDistributedDetails = $trnCrowdFundEvents->getTrnCrowdFundEventDistributedDetails();
                foreach ($arrTrnCrowdFundEventDistributedDetails as $trnCrowdFundEventDistributedDetails){
                    $trnCrowdFundEvents->removeTrnCrowdFundEventDistributedDetail($trnCrowdFundEventDistributedDetails);
                }

                $arrTrnCrowdFundEventDocuments = $trnCrowdFundEvents->getTrnCrowdFundEventDocuments();
                foreach ($arrTrnCrowdFundEventDocuments as $trnCrowdFundEventDocuments) {
                    $trnCrowdFundEvents->removeTrnCrowdFundEventDocument($trnCrowdFundEventDocuments);
                }

                $entityManager->persist($trnCrowdFundEvents);
                $entityManager->flush();
                $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
                foreach ($crowdFundRaiserSubEvents as $crowdFundRaiserSubEvent) {
                    $objTrnCrowdFundEventDistributedDetails = new TrnCrowdFundEventDistributedDetails();
                    $objTrnCrowdFundEventDistributedDetails->setCampaignerName($crowdFundRaiserSubEvent['memberName']);
                    $objTrnCrowdFundEventDistributedDetails->setMobileNumber($crowdFundRaiserSubEvent['memberMobileNumber']);
                    $objTrnCrowdFundEventDistributedDetails->setCampaingerEmail($crowdFundRaiserSubEvent['memberEmailId']);
                    $objTrnCrowdFundEventDistributedDetails->setTargetAmount($crowdFundRaiserSubEvent['distributeAmount']);
                    $objTrnCrowdFundEventDistributedDetails->setTrnCrowdFundEvent($trnCrowdFundEvents);
                    $entityManager->persist($objTrnCrowdFundEventDistributedDetails);
                    $trnCrowdFundEvents->addTrnCrowdFundEventDistributedDetail($objTrnCrowdFundEventDistributedDetails);
                }
                $entityManager->flush();
            }*/

            $crowdfundingEventService->addCrowdfundEventMedia($trnCircleEvents, $file, $youTubeArr,
                $primaryAreaOfInterest, $arrSecondaryAI, $documentDescription, $companyId,
                $this->getParameter('public_file_folder'), $reportingArr, $uploadedDocuments);

            /*
             * Remove any Previous Data If Any
             * */
            /*if (!empty($file)) {
                $objProfileImageData = $request->files->get('profileImage');
                $objBackGroundImage = $request->files->get('backGroundImage');
                $arrImageGallery = $request->files->get('imageGallery');
                $arrFilename = $request->files->get('filename');
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
                if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0]) && !empty($arrFilename)) {
                    $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
                    foreach ($arrFilename as $key => $objFilename) {
                        $strDocDesc = "";
                        if (!empty($documentDescription) && !empty($documentDescription[$key])){
                            $strDocDesc = $documentDescription[$key];
                        }
                        if (!empty($objFilename)) {
                            $newFilename = $fileUploaderHelper->uploadPublicFile($objFilename, 'EventDocumentImage'
                                .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                            $objTrnCrowdFundEventDocuments = new TrnCrowdFundEventDocuments();
                            $objTrnCrowdFundEventDocuments->setUploadedFilePath($newFilename);
                            $objTrnCrowdFundEventDocuments->setDocumentCaption($strDocDesc);
                            $objTrnCrowdFundEventDocuments->setTrnCrowdFundEvent($trnCrowdFundEvents);
                            $objTrnCrowdFundEventDocuments->setIsActive(1);
                            $entityManager->persist($objTrnCrowdFundEventDocuments);
                            $trnCrowdFundEvents->addTrnCrowdFundEventDocument($objTrnCrowdFundEventDocuments);
                        }
                    }
                }
            }
            if (!empty($youTubeArr)) {
                foreach ($youTubeArr as $key => $youtubeLink) {
                    if (empty($youtubeLink))
                        continue;
                    $trnProductMedia = new TrnProductMedia();
                    $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                    $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                    $trnProductMedia->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                    $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                    $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                    $trnProductMedia->setIsActive(1);
                    $trnProductMedia->setMediaType('video');
                    $trnProductMedia->setMediaName('YoutubeLink'.$trnCircleEvents->getId().$key);
                    $trnProductMedia->setMediaURL($youtubeLink);
                    $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                    $trnProductMedia->setCreatedOn(new DateTime());
                    $entityManager->persist($trnProductMedia);
                    $trnCircleEvents->addTrnProductMedium($trnProductMedia);
                }
            }*/
            $entityManager->flush();
            $session->set('trnCircleEventId', $trnCircleEvents->getId());

            //Event Created Creator
            /*

            // Moved notification to thank you page
            $notificationService->setAppUser($trnCircleEvents->getAppUser());
            $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
            $notificationService->setTrnCircleEvents($trnCircleEvents);
            $notificationService->doProcess('Event Created Creator');
            $notificationService->doGCProcess('Event Created GC');
            */

            //Create Distributed Events
            if($request->get('submission_type') == 'review') {
                // if review clicked, create sub event in review >> post form submission block
            } else {
                $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
                    foreach ($crowdFundRaiserSubEvents as $crowdFundRaiserSubEvent) {
                        $myAccountService->createDistributeEvent($trnCircleEvents, $crowdFundRaiserSubEvent['memberId'], $crowdFundRaiserSubEvent['distributeAmount']);
                }
            }
            //Create Distributed Events

            // Add bank details to my account if not there
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

            if($request->get('submission_type') == 'review') {
                return $this->redirectToRoute('create-crowdfunding-event-review');
            } else {
                return $this->redirectToRoute('create-crowdfunding-event-thank-you');
            }
        }
        $arrMstBankAccountType = $this->getDoctrine()->getRepository(MstBankAccountType::class)->findAllActive();

        // this is set as they are used in edit
        $arrPrimaryAISelected = $arrSecondaryAISelected = $arrImages = array();

        return $this->render('portal/crowdfunding-event/create-crowdfunding-event.html.twig', [
            'form' => $form->createView(),
            'arrMstBankAccountType' => $arrMstBankAccountType,
            'isEditEvent' => false,
            'trnCircleEvents' => $trnCircleEvents,
            'arrPrimaryAISelected' => $arrPrimaryAISelected,
            'arrSecondaryAISelected' => $arrSecondaryAISelected,
            'arrImages' => $arrImages
        ]);
    }


    /**
     * @Route("/edit-crowdfunding-event", name="edit-crowdfunding-event", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @param MyAccountService $myAccountService
     * @param CrowdfundingEventService $crowdfundingEventService
     * @return Response
     */
    public function editCrowdfundingEvent(Request $request, SessionInterface $session,
                                            TokenStorageInterface $tokenStorage, AppUserRepository
                                            $appUserRepository, MyAccountService $myAccountService,
                                            CrowdfundingEventService $crowdfundingEventService): Response
    {
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-crowdfunding-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');

        $form = $this->createForm(TrnCircleEventsCrowdFundEventPortalType::class, $trnCircleEvents, array('mstStatus' => $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Activated'])));
        $form->handleRequest($request);
        $userId = $tokenStorage->getToken()->getUser();
        $appUser = $appUserRepository->find($userId);

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
                $existingGalleryImgs[] = $trnProductMedia;
            } else if(strtolower($trnProductMedia->getMediaType()) == 'video' && $trnProductMedia->getMediaUrl()) {
                $arrUrl[] = $trnProductMedia->getMediaUrl();
                $existingUrls[] = $trnProductMedia;
            }
        }


        if ($request->isMethod('POST')) {

//            dd($request);

            if (empty($trnCircleEventId))
                return $this->redirectToRoute('create-crowdfunding-event');
            $newCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);

            $companyId = $this->getParameter('company_id');
            $uploadedDocuments = $request->get('uploadedDoc');

            $file = array();
            $youTubeArr = array();
            $reportingArr = $request->get('reporting');
            $preloadedGalImgList = array();
            if((isset($reportingArr['image']) && $reportingArr['image'] == 'on') || !empty($request->files->get('filename'))  ) {
                $file = $request->files;
                $preloadedGalImgArr = $request->get('preloadedGalImg', array());
                foreach ($preloadedGalImgArr as $galImg) {
                    $preloadedGalImgList[] = str_replace('/images/files/', '', $galImg);
                }
            }
            if(isset($reportingArr['video']) && $reportingArr['video'] == 'on') {
                $youTubeArr = $request->get('youTube');
            }

            $primaryAreaOfInterest = $request->get('primaryAreaOfInterest');
            $secondaryAreaOfInterest = $request->get('secondaryAreaOfInterest');
            $documentDescription = $request->get('documentDescription');
            $arrSecondaryAI = explode(',', $secondaryAreaOfInterest);

            $entityManager = $this->getDoctrine()->getManager();

            // Removed existing area of interest
            $existingSecondaryAI =  $trnCircleEvents->getTrnAreaOfInterests();
            foreach ($existingSecondaryAI as $TrnAreaOfInterest) {

                $this->getDoctrine()->getManager()->remove($TrnAreaOfInterest);
                $this->getDoctrine()->getManager()->flush();
            }

            $crowdfundingEventService->addEventDetails($newCircleEvents, $primaryAreaOfInterest, $appUser, $arrSecondaryAI, $companyId);
            $crowdfundingEventService->addCrowdfundEventDetails($newCircleEvents, $session);

            // Remove old media
            $existingTrnCircleMedia = $trnCircleEvents->getTrnProductMedia();
            foreach ($existingTrnCircleMedia as $trnCircleMedia) {
                if(!in_array($trnCircleMedia->getMediaFileName(),$preloadedGalImgList)) {
                    $trnCircleEvents->removeTrnProductMedium($trnCircleMedia);
                }
            }

            $crowdfundingEventService->addCrowdfundEventMedia($newCircleEvents, $file, $youTubeArr,
                $primaryAreaOfInterest, $arrSecondaryAI, $documentDescription, $companyId,
                $this->getParameter('public_file_folder'), $reportingArr, $uploadedDocuments);

            $entityManager->flush();

            if($request->get('submission_type') == 'review') {
                return $this->redirectToRoute('create-crowdfunding-event-review');
            } else {
                return $this->redirectToRoute('create-crowdfunding-event-thank-you');
            }
        }
        $arrMstBankAccountType = $this->getDoctrine()->getRepository(MstBankAccountType::class)->findAllActive();

        return $this->render('portal/crowdfunding-event/create-crowdfunding-event.html.twig', [
            'form' => $form->createView(),
            'arrMstBankAccountType' => $arrMstBankAccountType,
            'isEditEvent' => true,
            'trnCircleEvents' => $trnCircleEvents,
            'arrPrimaryAISelected' => $arrPrimaryAISelected,
            'arrSecondaryAISelected' => $arrSecondaryAISelected,
            'arrImages' => $arrImages
        ]);
    }

    /**
     * @Route("/create-crowdfunding-event-review", name="create-crowdfunding-event-review", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function createCrowdfundingEventReview(Request $request, SessionInterface $session, MyAccountService $myAccountService): Response
    {
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('create-crowdfunding-event');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('create-an-event');
        if ($request->isMethod('POST')) {

            //Create Distributed Events - if came from edit, create distributed events here
            $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
            foreach ($crowdFundRaiserSubEvents as $crowdFundRaiserSubEvent) {
                $myAccountService->createDistributeEvent($trnCircleEvents, $crowdFundRaiserSubEvent['memberId'], $crowdFundRaiserSubEvent['distributeAmount']);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCircleEvents);
            $entityManager->flush();
            //Create Distributed Events

            /*$session->remove('crowdFundRaiserSubEvents');
            $session->remove('trnCircleEventId');*/

            return $this->redirectToRoute('create-crowdfunding-event-thank-you');
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

        $bHasProfileImage = $bHasOtherImages = $bHasVideos = false;
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
            foreach ($trnCircleEvents->getTrnProductMedia() as $TrnProductMedia) {
                if ($TrnProductMedia->getMediaType() == 'video') {
                    $bHasVideos = true;
                } else {
                    $bHasOtherImages = true;
                }
            }
        }
        if( $trnCircleEvents->getProfileImage()!= 'files/')
            $bHasProfileImage = true;
        $trnCircle = $trnCircleEvents->getTrnCircle();
        return $this->render('portal/crowdfunding-event/create-crowdfunding-event-review.html.twig', ['trnCircleEvents' =>
            $trnCircleEvents, 'currentIndex' => 1000, 'arrPrimaryAISecAI' => $arrPrimaryAISecAI,  'arrPrimaryAI' =>
            $arrPrimaryAI, 'trnCircle' => $trnCircle, 'trnCrowdFundEvents' => $trnCrowdFundEvents,
            'bHasProfileImage' => $bHasProfileImage,'bHasVideos' => $bHasVideos,'bHasOtherImages' => $bHasOtherImages]);
    }

    /**
     * @Route("/create-crowdfunding-event-thank-you", name="create-crowdfunding-event-thank-you", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @param NotificationService $notificationService
     * @return Response
     */
    public function createCrowdfundingEventThankYou(Request $request, SessionInterface $session, MyAccountService $myAccountService,
                                                    NotificationService $notificationService): Response
    {
        if($session->has('trnCircleEventId')) {

            $trnCircleEventId = $session->get('trnCircleEventId', array());
            $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);

            $trnCircleEvents->setIsActive(1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trnCircleEvents);
            $entityManager->flush();

            $distributedEvents = $myAccountService->getDistributedEvents($trnCircleEvents);

            foreach ($distributedEvents as $distributedEvent) {
                $distributedEvent->setIsActive(1);
                $entityManager->persist($distributedEvent);
            }
            $entityManager->flush();

            //Event Created Creator
            $notificationService->setAppUser($trnCircleEvents->getAppUser());
            $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
            $notificationService->setTrnCircleEvents($trnCircleEvents);
            $notificationService->doProcess('Event Created Creator');
            $notificationService->doGCProcess('Event Created GC');
        }

        $session->remove('crowdFundRaiserSubEvents');
        $session->remove('trnCircleEventId');

        return $this->render('portal/crowdfunding-event/create-crowdfunding-event-thank-you.html.twig', []);
    }

    /**
     * @Route("/get-project-bank-details", name="get-project-bank-details", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function getProjectBankDetails(Request $request) {
        $nProjectId  = $request->get('nProjectId');
        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($nProjectId);
        return $this->render('portal/crowdfunding-event/_ajax-create-bank-details.html.twig', ['trnCircle' => $objTrnCircle]);
    }

    /**
     * @Route("/add-crowd-funding-sub-event-to-session", name="add-crowd-funding-sub-event-to-session", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function addCrowFundingSubEventToSession(Request $request, SessionInterface $session){
        $memberName         = $request->get('memberName');
        $memberId         = $request->get('memberId');
        $memberMobileNumber = $request->get('memberMobileNumber');
        $memberEmailId 		= $request->get('memberEmailId');
        $distributeAmount 	= $request->get('distributeAmount');

        $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
        $crowdFundRaiserSubEvents[] = array( 'memberName' => $memberName,'memberId' => $memberId,
            'memberMobileNumber' => $memberMobileNumber, 'memberEmailId' => $memberEmailId,
            'distributeAmount' => $distributeAmount);
        $session->set('crowdFundRaiserSubEvents', $crowdFundRaiserSubEvents);
        return $this->render('portal/crowdfunding-event/_ajax-get-crowdfund-sub-event.html.twig', [ 'crowdFundRaiserSubEvents' =>
            $crowdFundRaiserSubEvents]);
    }

    /**
     * @Route("/load-crowd-funding-sub-event-from-session", name="load-crowd-funding-sub-event-from-session", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function loadCrowFundingSubEventFromSession(Request $request, SessionInterface $session){

        $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());

        return $this->render('portal/crowdfunding-event/_ajax-get-crowdfund-sub-event.html.twig', [ 'crowdFundRaiserSubEvents' =>
            $crowdFundRaiserSubEvents]);
    }

    /**
     * @Route("/remove-crowd-funding-sub-event-to-session", name="remove-crowd-funding-sub-event-to-session", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function removeCrowdFundingSubEventToSession(Request $request, SessionInterface $session) {
        $keyToRemove = $request->get('key');
        $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
        if (!empty($crowdFundRaiserSubEvents) && !empty($crowdFundRaiserSubEvents[$keyToRemove])){
            unset($crowdFundRaiserSubEvents[$keyToRemove]);
        }
        $session->set('crowdFundRaiserSubEvents', $crowdFundRaiserSubEvents);
        return $this->json(['message' => 'Successfully updated']);
    }

    /**
     * @Route("/remove_crowdfunding_gallery_image", name="remove_crowdfunding_gallery_image", methods={"GET","POST"})
     * @param Request $request
     * FileUploaderHelper $fileUploaderHelper
     * @return Response
     */
    public function removeCrowdfundingGalleryImage(Request $request, FileUploaderHelper $fileUploaderHelper): Response
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
     * @Route("/get-project-member-list", name="get-project-member-list", methods={"GET", "POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @param TrnCircleRepository $trnCircleRepository
     * @return void
     */
    public function getProjectMemberList(Request $request, MyAccountService $myAccountService, TrnCircleRepository $trnCircleRepository) {
        if($request->isMethod('POST')) {
            $circleId = $request->get('nProjectId');
            $trnCircle =  $trnCircleRepository->find($circleId);
            $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircle);
            return $this->render('portal/crowdfunding-event/_ajax-project-member-option.html.twig', [
                "arrContributorData" => $arrProjectMemberListData["arrContributorData"]
            ]);
        }
    }
}