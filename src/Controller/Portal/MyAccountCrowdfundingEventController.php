<?php

namespace App\Controller\Portal;

use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEventDistributedDetails;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Service\FileUploaderHelper;
use App\Service\MyAccountService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class MyAccountEventController
 * @IsGranted("ROLE_APP_USER")
 */
class MyAccountCrowdfundingEventController extends AbstractController
{
    /**
     * @var array
     */
    private $arrSocialProfileData;

    /**
     * @Route("/my-account/crowdfunding-event-info", name="my-account-crowdfunding-event-info", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function crowdfundingEventInfo(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $arrOwnCrowdfundingEventData = $myAccountService->getOwnCrowdfundingEventData($appUser, $this->getParameter('company_id'));
        $distributedEvents = $myAccountService->getDistributedEventMultiEvents($arrOwnCrowdfundingEventData);
        $nCrowdFundingEvents = count($arrOwnCrowdfundingEventData) + count($distributedEvents);
        return $this->render('portal/my-account/crowdfunding/crowdfunding-event-info.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'nCrowdFundingEvents' =>
                $nCrowdFundingEvents, 'distributedEvents' => $distributedEvents, 'arrOwnCrowdfundingEventData' => $arrOwnCrowdfundingEventData
        ]);
    }

    /**
     * @Route("/my-account/crowdfunding-event-listing", name="my-account-crowdfunding-event-listing", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function crowdfundingEventListing(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnCrowdfundingEventData = $myAccountService->getOwnCrowdfundingEventData($appUser, $this->getParameter('company_id'));
        $distributedEvents = $myAccountService->getDistributedEventMultiEvents($arrOwnCrowdfundingEventData);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ($arrOwnCrowdfundingEventData, $entityManager);
        return $this->render('portal/my-account/crowdfunding/crowdfunding-event-listing.html.twig', [
            'arrOwnCrowdfundingEventData' => $arrOwnCrowdfundingEventData, 'arrEventUpComingOrOnGoingDetails' =>
                $arrEventUpComingOrOnGoingDetails, 'distributedEvents' => $distributedEvents]);
    }

    /**
     * @Route("/my-account/crowdfunding-empty-event", name="my-account-crowdfunding-empty-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function crowdfundingEmptyEvent(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService): Response
    {
        return $this->render('portal/my-account/crowdfunding/crowdfunding-empty-event.html.twig', []);
    }

    /**
     * @Route("/my-account/crowdfunding-distribute-campaign/{id}", name="my-account-crowdfunding-distribute-campaign", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param AppUserRepository $appUserRepository
     * @return Response
     */
    public function distributeCampaign(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository,
                                       AppUserRepository $appUserRepository): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        (array($trnCircleEvents), $entityManager);
        if ($request->isMethod('POST')) {
            $selectContributor = $request->get('selectContributor');
            $targetAmount = $request->get('targetAmount');
            $myAccountService->createDistributeEvent($trnCircleEvents, $selectContributor, $targetAmount);
            $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
            if (!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])) {
                $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
                if (!empty($trnCrowdFundEvents)) {
                    $trnCrowdFundEvents->setIsDistributedEvent(1);
                    $entityManager->persist($trnCrowdFundEvents);
                    $appUserContributor = $appUserRepository->find($selectContributor);
                    $objTrnCrowdFundEventDistributedDetails = new TrnCrowdFundEventDistributedDetails();
                    $objTrnCrowdFundEventDistributedDetails->setCampaignerName($appUserContributor->getAppUserInfo()->getUserFirstName() . ' ' . $appUser->getAppUserInfo()->getUserLastName());
                    $objTrnCrowdFundEventDistributedDetails->setMobileNumber($appUserContributor->getAppUserInfo()->getUserMobileNumber());
                    $objTrnCrowdFundEventDistributedDetails->setCampaingerEmail($appUserContributor->getAppUserInfo()->getUserEmail());
                    $objTrnCrowdFundEventDistributedDetails->setTargetAmount($targetAmount);
                    $objTrnCrowdFundEventDistributedDetails->setTrnCrowdFundEvent($trnCrowdFundEvents);
                    $entityManager->persist($objTrnCrowdFundEventDistributedDetails);
                    $trnCrowdFundEvents->addTrnCrowdFundEventDistributedDetail($objTrnCrowdFundEventDistributedDetails);
                    $entityManager->flush();
                }
            }
            $this->addFlash('success', 'Campaign Distributed successfully.');
            return $this->redirectToRoute('my-account-crowdfunding-event-info');
        }
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle());
        $arrDistributedEvents = $myAccountService->getDistributedEvents($trnCircleEvents);
        return $this->render('portal/my-account/crowdfunding/crowdfunding-distribute-campaign.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            "arrContributorData" => $arrProjectMemberListData["arrContributorData"],
            'arrDistributedEvents' => $arrDistributedEvents
        ]);
    }

    /**
     * @Route("/my-account/edit-crowdfunding-event/{id}", name="my-account-edit-crowdfunding-event", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param SessionInterface $session
     * @param MyAccountService $myAccountService
     * @return Response
     */
    public function editCrowdFundingEvent(Request $request, SessionInterface $session): Response
    {
        $session->set('trnCircleEventId', $request->get('id'));
        $trnCircleEventId = $session->get('trnCircleEventId', array());
        if (empty($trnCircleEventId))
            return $this->redirectToRoute('my-account-crowdfunding-event-listing');
        $trnCircleEvents = $this->getDoctrine()->getRepository(TrnCircleEvents  ::class)->find($trnCircleEventId);
        if (empty($trnCircleEvents))
            return $this->redirectToRoute('my-account-crowdfunding-event-listing');

        $crowdFundRaiserSubEvents = array();

        $trnCrowdFundEvents = null;
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])) {
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];

            $arrTrnCrowdFundEventDistributedDetails = $trnCrowdFundEvents->getTrnCrowdFundEventDistributedDetails();
            foreach ($arrTrnCrowdFundEventDistributedDetails as $trnCrowdFundEventDistributedDetails) {

                // TODO: get memberid i.e. userid based on appuser table

                $crowdFundRaiserSubEvents[] = array( 'memberName' => $trnCrowdFundEventDistributedDetails->getCampaignerName(),
                    'memberId' => $trnCrowdFundEventDistributedDetails->getId(),
                    'memberMobileNumber' => $trnCrowdFundEventDistributedDetails->getMobileNumber(),
                    'memberEmailId' => $trnCrowdFundEventDistributedDetails->getCampaingerEmail(),
                    'distributeAmount' => $trnCrowdFundEventDistributedDetails->getTargetAmount());
            }
        }
        $session->set('crowdFundRaiserSubEvents', $crowdFundRaiserSubEvents);

        return $this->redirectToRoute('edit-crowdfunding-event');

        //return $this->render('portal/my-account/crowdfunding/crowdfunding-distribute-campaign.html.twig', []);
    }

    /**
     * @Route("/my-account/crowdfunding-updates/{id}", name="my-account-crowdfunding-updates", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws \Exception
     */
    public function crowdFundingUpdates(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository,
                                        FileUploaderHelper $fileUploaderHelper): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        (array($trnCircleEvents), $entityManager);
        $arrBroadCastMessages = $myAccountService->getEventBroadCastMessages($trnCircleEvents);
        if ($request->isMethod('POST')) {
            $textBroadCastMessage = $request->get('textBroadCastMessage');
            $hdnSentTo = $request->get('hdnSentTo');
            $checkBoxNameToSubmit = array();
            if ($hdnSentTo == 'donors') {
                $checkBoxNameToSubmit = $myAccountService->getDonorList($trnCircleEvents);
            }
            $filename = $request->files->get('filename');
            $newFilename = "";
            $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
            if (!empty($filename))
                $newFilename = $fileUploaderHelper->uploadPublicFile($filename, 'Document' . Uuid::uuid4()->toString(), null);
            $myAccountService->eventBroadcastUpdateCF($trnCircleEvents, $checkBoxNameToSubmit, $textBroadCastMessage,
                $hdnSentTo, $newFilename, $orgCompany);
            if ($hdnSentTo == 'donors') {
                $this->addFlash('success', 'Broadcast update successfully sent.');
            } else {
                $this->addFlash('success', 'Broadcast update is successfully done on website');
            }
            return $this->redirectToRoute('my-account-crowdfunding-event-info');
        }
        $donorList = $myAccountService->getDonorList($trnCircleEvents);
        return $this->render('portal/my-account/crowdfunding/crowdfunding-updates.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrBroadCastMessages' => $arrBroadCastMessages, 'countDonor' => count($donorList)]);
    }

    /**
     * @Route("/my-account/crowdfunding-event-deactivate/{id}", name="my-account-crowdfunding-event-deactivate", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function crowdFundingEventDeactivate(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository): Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        (array($trnCircleEvents), $entityManager);
        if ($request->isMethod('POST')) {
            $deactivateEventReason = $request->get('deactivateEventReason');
            $myAccountService->deactivateEvent($trnCircleEvents, $deactivateEventReason);
            $this->addFlash('success', 'Event successfully deactivated.');
            return $this->redirectToRoute('my-account-crowdfunding-event-info');
        }
        return $this->render('portal/my-account/crowdfunding/crowdfunding-event-deactivate.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails]);
    }

    /**
     * @Route("/my-account/crowdfunding-deactivate-distributed-event/{id}", name="my-account-crowdfunding-deactivate-distributed-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function deactivateCFDistributedEvent(Request $request, TrnCircleEvents $trnCircleEvents): Response
    {
        return $this->render('portal/my-account/crowdfunding/crowdfunding-deactivate-distributed-event.html.twig', ['eventData'
        => $trnCircleEvents]);
    }

    /**
     * @Route("/my-account/ajax-crowdfunding-event-listing", name="my-account-ajax-crowdfunding-event-listing", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function _ajaxCrowdfundingEventListing(Request $request, TokenStorageInterface $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository): Response
    {
        if ($request->isMethod('POST')) {
            $arrParameters = array();
            $arrParameters['quicksearch'] = $request->get('quicksearch');
            $arrParameters['from'] = $request->get('from');
            $arrParameters['to'] = $request->get('to');
            $appUser = $tokenStorage->getToken()->getUser();
            $arrOwnCrowdfundingEventData = $myAccountService->getOwnCrowdfundingEventData($appUser, $this->getParameter('company_id'), $arrParameters);
            $distributedEvents = $myAccountService->getDistributedEventMultiEvents($arrOwnCrowdfundingEventData, $arrParameters);
            $entityManager = $this->getDoctrine()->getManager();
            $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
            ($arrOwnCrowdfundingEventData, $entityManager);
            return $this->render('portal/my-account/crowdfunding/_ajax-crowdfunding-event-listing.html.twig', [
                'arrOwnCrowdfundingEventData' => $arrOwnCrowdfundingEventData, 'arrEventUpComingOrOnGoingDetails' =>
                    $arrEventUpComingOrOnGoingDetails, 'distributedEvents' => $distributedEvents]);
        }
    }

    /**
     * @Route("/my-account/crowdfunding-donation-event/{id}", name="my-account-crowdfunding-donation-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function crowdfundingEventDonation(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService
    $myAccountService, TokenStorageInterface $tokenStorage, TrnCircleEventsRepository $trnCircleEventsRepository)
    : Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $DonationInformation =  $myAccountService->getDonationInformation($trnCircleEvents);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        (array($trnCircleEvents), $entityManager);
        $bEnableAction = true;
        if(!empty($trnCircleEvents->getParentTrnCircleEvents())){
            $bEnableAction = false;
        }
        return $this->render('portal/my-account/crowdfunding/crowdfunding-donation-event.html.twig', ['eventData'
        => $trnCircleEvents, 'arrDonationInformation' => $DonationInformation, 'appUser' => $appUser, 'arrEventUpComingOrOnGoingDetails' =>
                    $arrEventUpComingOrOnGoingDetails, 'bEnableAction' => $bEnableAction ]);
    }

    /**
     * @Route("/my-account/ajax-update-offline-payment-status", name="my-account-ajax-update-offline-payment-status", methods={"POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @return JsonResponse
     */
    public function ajaxUpdateOfflinePaymentStatus(Request $request,MyAccountService $myAccountService) :JsonResponse {
        $requestId = $request->get('requestid');
        $strStatusName = $request->get('strStatusName');
        $arrResponse = $myAccountService->updateOfflinePaymentStatus($requestId, $strStatusName);
        return new JsonResponse($arrResponse);
    }

    /**
     * @Route("/my-account/ajax-crowdfunding-donation-event/{id}", name="my-account-ajax-crowdfunding-donation-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function ajaxCrowdfundingEventDonation(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService
    $myAccountService, TokenStorageInterface $tokenStorage, TrnCircleEventsRepository $trnCircleEventsRepository)
    : Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $DonationInformation =  $myAccountService->getDonationInformation($trnCircleEvents);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        (array($trnCircleEvents), $entityManager);
        $bEnableAction = true;
        if(!empty($trnCircleEvents->getParentTrnCircleEvents())){
            $bEnableAction = false;
        }
        return $this->render('portal/my-account/crowdfunding/_ajax-crowdfunding-donation-event.html.twig', ['eventData'
        => $trnCircleEvents, 'arrDonationInformation' => $DonationInformation, 'appUser' => $appUser, 'arrEventUpComingOrOnGoingDetails' =>
            $arrEventUpComingOrOnGoingDetails, 'bEnableAction' => $bEnableAction]);
    }
}