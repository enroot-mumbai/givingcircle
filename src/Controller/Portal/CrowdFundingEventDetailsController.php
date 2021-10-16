<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircleEventBroadCastDetails;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use App\Entity\Transaction\TrnOrder;
use App\Form\Transaction\TrnCrowdFundEventOfflineTransferType;
use App\Form\Transaction\TrnUserCommentsType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\Cms\CmsArticleRepository;
use App\Service\MyAccountService;
use App\Service\OrderDetails;
use App\Service\ProjectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class CrowdFundingEventDetailsController
 */
class CrowdFundingEventDetailsController extends AbstractController
{

    /**
     * @Route("/crowd-funding-event-details/{id}", name="crowd-funding-event-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param ProjectService $projectService
     * @return Response
     */
    public function crowdFundingEventDetails(Request $request, TrnCircleEvents $trnCircleEvents, ProjectService $projectService) {
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }
        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);

        /*$trnCircleEventOfflineTransfer = new TrnCrowdFundEventOfflineTransfer();
        $offlineTransferForm = $this->createForm(TrnCrowdFundEventOfflineTransferType::class, $trnCircleEventOfflineTransfer);
        $offlineTransferForm->handleRequest($request);*/

        $projectService->makeEntryForVisitor($trnCircleEvents->getTrnCircle(), $trnCircleEvents);

        // Increase read count
        $entityManager = $this->getDoctrine()->getManager();
        $ReadCount = $trnCircleEvents->getReadCount();
        if(empty($ReadCount))
            $ReadCount =0 ;
        $ReadCount++;
        $trnCircleEvents->setReadCount($ReadCount);
        $entityManager->persist($trnCircleEvents);
        $entityManager->flush();
        return $this->render('portal/crowdfunding-event/crowd-funding-event-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnCrowdFundEvents' => $trnCrowdFundEvents, 'form' => $form->createView(),
            'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
//            'offlineTransferForm' => $offlineTransferForm->createView()
        ]);
    }

    /**
     * @Route("/crowd-funding-header-section/{id}", name="crowd-funding-header-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingHeaderSection(Request $request, TrnCircleEvents $trnCircleEvents) {
        return $this->render('portal/crowdfunding-event/crowd-funding-header-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-banner-section/{id}", name="crowd-funding-banner-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingBannerSection(Request $request, TrnCircleEvents $trnCircleEvents) {
        return $this->render('portal/crowdfunding-event/crowd-funding-banner-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-appeal-section/{id}", name="crowd-funding-appeal-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingAppealSection(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService $myAccountService) {
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }
        $arrBroadCastMessages = $myAccountService->getEventBroadCastMessages($trnCircleEvents, ['createdOn' => 'DESC']);
        $parentId = $trnCircleEvents->getParentTrnCircleEvents();

        $arrBroadCastMessages = $this->getDoctrine()->getRepository(TrnCircleEventBroadCastDetails::class)->getEventNParentBroadCastMessages($trnCircleEvents, $parentId, ['createdOn' => 'DESC']);

        return $this->render('portal/crowdfunding-event/crowd-funding-appeal-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnCrowdFundEvents' => $trnCrowdFundEvents,
            'arrBroadCastMessages' => $arrBroadCastMessages
        ]);
    }

    /**
     * @Route("/crowd-funding-campaign-raiser-section/{id}", name="crowd-funding-campaign-raiser-section",
     *     methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingCampaignRaiserSection(Request $request, TrnCircleEvents $trnCircleEvents) {
        return $this->render('portal/crowdfunding-event/crowd-funding-campaign-raiser-section.html.twig', [
            'circle' => $trnCircleEvents->getTrnCircle()
        ]);
    }

    /**
     * @Route("/crowd-funding-maker-stories-section", name="crowd-funding-maker-stories-section",
     *     methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param CmsArticleRepository $cmsArticleRepository
     * @return Response
     */
    public function crowdFundingMakerStoriesSection(Request $request, TrnCircleEvents $trnCircleEvents,
                                                    CmsArticleRepository $cmsArticleRepository) {
        $ChangeMakerStories = $cmsArticleRepository->findOneBy(['mstArticleCategory' => 2,
            'orgCompany' => $this->getParameter('company_id'), 'isActive' => 1,
            'changeMakerAppUser' => $trnCircleEvents->getAppUser()], ['articleCreateDateTime' => 'DESC',
            'sequenceNo' => 'ASC']);
        return $this->render('portal/crowdfunding-event/crowd-fund-change-maker-stories.html.twig', [
            'ChangeMakerStories' => $ChangeMakerStories
        ]);
    }

    /**
     * @Route("/crowd-funding-form-contribute", name="crowd-funding-form-contribute",
     *     methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingFormContribute(Request $request, TrnCircleEvents $trnCircleEvents) {
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }
        return $this->render('portal/crowdfunding-event/crowd-funding-form-contribute.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnCrowdFundEvents' => $trnCrowdFundEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-fund-raiser", name="crowd-funding-fund-raiser", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingFundRaiser(Request $request, TrnCircleEvents $trnCircleEvents) {
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }
        return $this->render('portal/crowdfunding-event/crowd-funding-fund-raiser.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnCrowdFundEvents' => $trnCrowdFundEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-campaign-organizer-details", name="crowd-funding-campaign-organizer-details", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingCampaignOrganizerDetails(Request $request, TrnCircleEvents $trnCircleEvents) {
        $objAppUser = $trnCircleEvents->getAppUser();
        $objAppUserInfo = $trnCircleEvents->getAppUser()->getAppUserInfo();
        return $this->render('portal/crowdfunding-event/crowd-funding-campaign-organizer-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'objAppUser' => $objAppUser ,  'objAppUserInfo' => $objAppUserInfo
        ]);
    }

    /**
     * @Route("/crowd-funding-project-glimpses", name="crowd-funding-project-glimpses", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingProjectGlimpses(Request $request, TrnCircleEvents $trnCircleEvents) {
        $objAppUser = $trnCircleEvents->getAppUser();
        $objAppUserInfo = $trnCircleEvents->getAppUser()->getAppUserInfo();

        return $this->render('portal/crowdfunding-event/crowd-funding-project-glimpses.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'objAppUser' => $objAppUser ,  'objAppUserInfo' => $objAppUserInfo,
            'circle' => $trnCircleEvents->getTrnCircle()
        ]);
    }

    /**
     * @Route("/crowd-funding-team", name="crowd-funding-team", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingTeam(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService $myAccountService) {
        $arrDistributedEvents     = $myAccountService->getDistributedEvents($trnCircleEvents);
        return $this->render('portal/crowdfunding-event/crowd-funding-team.html.twig', [
            'trnCircleEvents' => $trnCircleEvents,
            'arrDistributedEvents' => $arrDistributedEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-top-donors", name="crowd-funding-top-donors", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param OrderDetails $orderDetails
     * @return Response
     */
    public function crowdFundingTopDonors(Request $request, TrnCircleEvents $trnCircleEvents, OrderDetails $orderDetails) {
        $arrTopDonors = $orderDetails->getTopDonors($trnCircleEvents);
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }
        return $this->render('portal/crowdfunding-event/crowd-funding-top-donors.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'arrTopDonors' => $arrTopDonors, 'trnCrowdFundEvents' =>
                $trnCrowdFundEvents
        ]);
    }

    /**
     * @Route("/crowd-funding-supporters", name="crowd-funding-supporters", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param OrderDetails $orderDetails
     * @return Response
     */
    public function crowdFundingSupporters(Request $request, TrnCircleEvents $trnCircleEvents, OrderDetails $orderDetails) {
        $arrLatestDonor = $orderDetails->getLatestDonor($trnCircleEvents);
        return $this->render('portal/crowdfunding-event/crowd-funding-supporters.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'arrLatestDonor' => $arrLatestDonor
        ]);
    }

    /**
     * @Route("/crowd-funding-cm-stories", name="crowd-funding-cm-stories", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param CmsArticleRepository $cmsArticleRepository
     * @return Response
     */
    public function crowdFundingCmStories(Request $request, TrnCircleEvents $trnCircleEvents, CmsArticleRepository
    $cmsArticleRepository) {

        $ChangeMakerStories = $cmsArticleRepository->findOneBy(['mstArticleCategory' => 2,
            'orgCompany' => $this->getParameter('company_id'), 'isActive' => 1,
            'changeMakerAppUser' => $trnCircleEvents->getAppUser()], ['articleCreateDateTime' => 'DESC',
            'sequenceNo' => 'ASC']);

        return $this->render('portal/crowdfunding-event/crowd-funding-cm-stories.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'ChangeMakerStories' => $ChangeMakerStories
        ]);
    }

    /**
     * @Route("/crowd-funding-modal", name="crowd-funding-modal", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function crowdFundingModal(Request $request, TrnCircleEvents $trnCircleEvents) {
        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
        }

        $trnCircleEventOfflineTransfer = new TrnCrowdFundEventOfflineTransfer();
        $offlineTransferForm = $this->createForm(TrnCrowdFundEventOfflineTransferType::class, $trnCircleEventOfflineTransfer);
        $offlineTransferForm->handleRequest($request);

        return $this->render('portal/crowdfunding-event/crowd-funding-modal.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnCrowdFundEvents' => $trnCrowdFundEvents,
            'offlineTransferForm' => $offlineTransferForm->createView()
        ]);
    }

    /**
     * @Route("/crowd-funding-get-all-supporters/{id}", name="crowd-funding-get-all-supporters", methods={"POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param OrderDetails $orderDetails
     * @return Response
     */
    public function crowdFundingGetAllSupporters(Request $request, TrnCircleEvents $trnCircleEvents, OrderDetails $orderDetails) {
        $arrAllDonor = $orderDetails->getAllDonor($trnCircleEvents);
        return $this->render('portal/crowdfunding-event/crowd-funding-get-all-supporters.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'arrAllDonor' => $arrAllDonor
        ]);
    }

    /**
     * @Route("/crowd-funding-offline-order-details", name="crowd-funding-offline-order-details", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function crowdFundingOfflineOrderDetails(Request $request) : Response
    {
        $crowdfundEventId = $request->get('crowdfundEventId');
        $eventId = $request->get('eventId');

        $formPostedValue = $request->get('trn_crowd_fund_event_offline_transfer');
        if(isset($formPostedValue['isAnonymousDonation'])) {
            $isAnonymousDonation = true;
        } else {
            $isAnonymousDonation = false;
        }
        $objCrowdFundEvent = $this->getDoctrine()->getRepository(TrnCrowdFundEvent::class)->find($crowdfundEventId);
        $objCircleEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);

        $entityManager = $this->getDoctrine()->getManager();
        $eventOfflineTransfer = new TrnCrowdFundEventOfflineTransfer();
        $eventOfflineTransfer->setTrnCrowdFundEvent($objCrowdFundEvent);
        $eventOfflineTransfer->setTrnCircleEvent($objCircleEvent);
        $eventOfflineTransfer->setBankTransactionId($formPostedValue['bankTransactionId']);
        $eventOfflineTransfer->setAmountDonated($formPostedValue['amountDonated']);
        $eventOfflineTransfer->setMstSalutation($this->getDoctrine()->getRepository(MstSalutation::class)->find($formPostedValue['mstSalutation']));
        $eventOfflineTransfer->setFirstName($formPostedValue['firstName']);
        $eventOfflineTransfer->setLastName($formPostedValue['lastName']);
        $eventOfflineTransfer->setIsAnonymousDonation($isAnonymousDonation);
        $eventOfflineTransfer->setEmail($formPostedValue['email']);
        $eventOfflineTransfer->setMobileCountryCode($formPostedValue['mobileCountryCode']);
        $eventOfflineTransfer->setMobileNumber($formPostedValue['mobileNumber']);
        $eventOfflineTransfer->setCreatedOn(new \DateTime());
        $eventOfflineTransfer->setUserIpAddress($_SERVER['SERVER_ADDR']);
        $eventOfflineTransfer->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" =>  'Pending Activation']));
        $eventOfflineTransfer->setIsActive(1);
        $entityManager->persist($eventOfflineTransfer);
        $entityManager->flush();

        return $this->json(array("detailsAdded" => true));
    }

}