<?php

namespace App\Controller\Portal;

use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnCircleEvents;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstStatusRepository;
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
class MyAccountEventController extends AbstractController
{
    /**
     * @var array
     */
    private $arrSocialProfileData;

    /**
     * @Route("/my-account/event-info", name="event-info", methods={"GET", "POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param SessionInterface $session
     * @return Response
     */
    public function eventInfo(Request $request,  TokenStorageInterface $tokenStorage, MyAccountService
    $myAccountService, SessionInterface $session)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $this->arrSocialProfileData = $myAccountService->getSocialProfileData($appUser);
        return $this->render('portal/my-account/event/event-info-start.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'arrSocialProfileData' => $this->arrSocialProfileData
        ]);
    }

    /**
     * @Route("/my-account/event-empty-info", name="my-account-event-empty-info", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function eventEmptyInfo(Request $request) :Response
    {
        return $this->render('portal/my-account/event/event-empty.html.twig', []);
    }

    /**
     * @Route("/my-account/event-listing", name="my-account-event-listing", methods={"GET", "POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function eventListing(Request $request, MyAccountService $myAccountService, TokenStorageInterface $tokenStorage) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrOwnEventData = $myAccountService->getOwnEventData($appUser, $this->getParameter('company_id'));

        return $this->render('portal/my-account/event/event-listing.html.twig', [
            'arrSocialProfileData' => $this->arrSocialProfileData, 'arrOwnEventData' => $arrOwnEventData['eventListing'],
            'totalEventCount' => $arrOwnEventData['totalEventCount'], 'arrEventUpComingOrOnGoingDetails' =>
                $arrOwnEventData['arrEventUpComingOrOnGoingDetails']
        ]);
    }

    /**
     * @Route("/my-account/send-reminder-for-event/{id}", name="my-account-send-reminder-for-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function sendReminderForEvent(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService $myAccountService,
                                         TokenStorageInterface $tokenStorage, TrnCircleEventsRepository
                                         $trnCircleEventsRepository , SessionInterface $session) :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle());
        $arrEventMemberListData = $myAccountService->getEventMemberList($trnCircleEvents);
        if($request->isMethod('POST')) {
            $checkBoxNameToSubmit = $request->get('checkBoxNameToSubmit');
            $myAccountService->sendReminderForEvent($trnCircleEvents, $checkBoxNameToSubmit, $arrEventUpComingOrOnGoingDetails);
            $this->addFlash('success', 'Reminder successfully sent.');
            return $this->redirectToRoute('event-info');
        }
        return $this->render('portal/my-account/event/send-reminder-for-event.html.twig', [
            'appUser'=> $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrContributorData' => $arrProjectMemberListData['arrContributorData'], 'participantData' =>
                $arrEventMemberListData['arrAppUserAccepted']
        ]);
    }

    /**
     * @Route("/my-account/add-view-lead-for-event/{id}", name="my-account-add-view-lead-for-event", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function addViewLeadForEvent(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface
    $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $projectEventLeadData = $myAccountService->getProjectEventLeadData($trnCircleEvents->getTrnCircle(), $trnCircleEvents);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle());
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);

        return $this->render('portal/my-account/event/add-view-lead-for-event.html.twig', [ 'appUser' => $appUser,
        'profileCompleteness' => $profileCompleteness, 'projectEventLeadData' => $projectEventLeadData,
         'arrProjectMemberListData' => $arrProjectMemberListData, 'circle' => $trnCircleEvents->getTrnCircle(),
         'eventData' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails ]);
    }

    /**
     * @Route("/my-account/ajax-add-view-lead-for-event/{id}", name="my-account-ajax-add-view-lead-for-event", methods={"POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function ajaxAddViewLeadForEvent(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface
    $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository)
    :Response {
        $appUser = $tokenStorage->getToken()->getUser();
        $arrParameters = array();
        $arrParameters['quicksearch'] = $request->get('quicksearch');
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $projectEventLeadData = $myAccountService->getProjectEventLeadData($trnCircleEvents->getTrnCircle(), $trnCircleEvents);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle(), $arrParameters);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);

        return $this->render('portal/my-account/event/_ajax-add-view-lead-for-event.html.twig', [ 'appUser' => $appUser,
        'profileCompleteness' => $profileCompleteness, 'projectEventLeadData' => $projectEventLeadData,
         'arrProjectMemberListData' => $arrProjectMemberListData, 'circle' => $trnCircleEvents->getTrnCircle(),
         'eventData' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails ]);
    }

    /**
     * @Route("/my-account/edit-event-data/{id}", name="my-account-edit-event-data", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param SessionInterface $session
     * @return Response
     */
    public function editEventData(Request $request, TrnCircleEvents $trnCircleEvents, SessionInterface $session) :Response {
        $session->set('trnCircleEventId', $trnCircleEvents->getId());
        return $this->redirectToRoute('create-an-event-review');
    }

    /**
     * @Route("/my-account/view-event-participant-list/{id}", name="my-account-view-event-participant-list", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @param MstStatusRepository $mstStatusRepository
     * @return Response
     */
    public function viewEventParticipantList(Request $request, TrnCircleEvents $trnCircleEvents,
                                             TokenStorageInterface $tokenStorage, MyAccountService $myAccountService,
                                             TrnCircleEventsRepository $trnCircleEventsRepository,
                                             MstEventProductTypeRepository $mstEventProductTypeRepository,
                                             MstStatusRepository $mstStatusRepository) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        $arrProjectMemberListData = $myAccountService->getEventMemberList($trnCircleEvents);
        $arrMstEventProductTypeObj = $mstEventProductTypeRepository->findBy(["isActive" => true]);
        $arrMstStatus              = $mstStatusRepository->findBy(["status" =>  array('Activated', 'Pending Activation')]);
        return $this->render('portal/my-account/event/view-event-participant-list.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrProjectMemberListData' => $arrProjectMemberListData, 'arrMstEventProductTypeObj' => $arrMstEventProductTypeObj,
            'arrMstStatus' => $arrMstStatus
        ]);
    }

    /**
     * @Route("/my-account/deactivate-event-data/{id}", name="my-account-deactivate-event-data", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function deactivateEventData(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface
    $tokenStorage, MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository)
    :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        if($request->isMethod('POST')){
            $deactivateEventReason = $request->get('deactivateEventReason');
            $myAccountService->deactivateEvent($trnCircleEvents, $deactivateEventReason);
            $this->addFlash('success', 'Event successfully deactivated.');
            return $this->redirectToRoute('event-info');
        }
        return $this->render('portal/my-account/event/deactivate-event-data.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
    }

    /**
     * @Route("/my-account/event-broadcast-update/{id}", name="my-account-event-broadcast-update", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws \Exception
     */
    public function eventBroadcastUpdate(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface $tokenStorage,
                                         MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository,
                                         FileUploaderHelper $fileUploaderHelper) :Response
    {
        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        $arrProjectMemberListData = $myAccountService->getProjectMemberList($trnCircleEvents->getTrnCircle());
        $arrEventMemberListData = $myAccountService->getEventMemberList($trnCircleEvents);
        $arrBroadCastMessages = $myAccountService->getEventBroadCastMessages($trnCircleEvents);
        if($request->isMethod('POST')) {
            $textBroadCastMessage = $request->get('textBroadCastMessage');
            $hdnBroadCastMembers = $request->get('hdnBroadCastMembers');
            $hdnSentTo = $request->get('hdnSentTo');
            $checkBoxNameToSubmit = explode(',', $hdnBroadCastMembers);
            $filename = $request->files->get('filename');
            $newFilename = "";
            $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
            if (!empty($filename)){
                $newFilename = $fileUploaderHelper->uploadPublicFile($filename, 'Document'.Uuid::uuid4()->toString(), null);
            }
            $myAccountService->eventBroadcastUpdate($trnCircleEvents, $checkBoxNameToSubmit, $textBroadCastMessage,
                $hdnSentTo, $newFilename, $orgCompany);
            $this->addFlash('success', 'Broadcast update successfully sent.');
            return $this->redirectToRoute('event-info');
        }
        return $this->render('portal/my-account/event/event-broadcast-update.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrContributorData' => $arrProjectMemberListData['arrContributorData'], 'participantData' =>
                $arrEventMemberListData['arrAppUserAccepted'], 'arrBroadCastMessages' => $arrBroadCastMessages]);
    }

    /**
     * @Route("/my-account/event-inline-details/{id}", name="my-account-event-inline-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function eventInlineDetails(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService $myAccountService,
                                       TrnCircleEventsRepository $trnCircleEventsRepository) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        return $this->render('portal/my-account/event/event-inline-details.html.twig', [
            'eventData' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails]);
    }

    /**
     * @Route("/my-account/exit-event-popup/{id}", name="my-account-exit-event-popup", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @return Response
     */
    public function exitEventPopUp(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository
    $trnCircleEventsRepository, MstEventProductTypeRepository $mstEventProductTypeRepository) :Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $strProductName = $request->get('strproductname');
        $joinDataId = $request->get('joindataid');
        $mstEventProductType = $mstEventProductTypeRepository->findOneBy(['eventProductType' => $strProductName]);
        $arrEventUpComingOrOnGoingDetails = $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        return $this->render('portal/my-account/event/_ajax-exit-event-popup.html.twig', [ 'mstEventProductType' => $mstEventProductType,
            'eventData' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'joinDataId' => $joinDataId]);

    }

    /**
     * @Route("/my-account/exit-event/{id}", name="my-account-exit-event", methods={"POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @return JsonResponse
     */
    public function exitEvent(Request $request, TrnCircleEvents $trnCircleEvents, MyAccountService $myAccountService,
                              TokenStorageInterface $tokenStorage) :JsonResponse {
        if($request->isMethod('POST')) {
            $productId = $request->get('productid');
            $joinDataId = $request->get('joindataid');
            $appUser = $tokenStorage->getToken()->getUser();
            $myAccountService->exitEvent($appUser, $trnCircleEvents, $productId, $joinDataId);
            return new JsonResponse([ 'Message' => 'Successfully exited from the event.']);
        }
        return new JsonResponse([ 'Message' => 'Invalid Method']);
    }

    /**
     * @Route("/my-account/ajax-event-listing", name="my-account-ajax-event-listing", methods={"POST"})
     * @param Request $request
     * @param MyAccountService $myAccountService
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     */
    public function ajaxEventListing(Request $request, MyAccountService $myAccountService, TokenStorageInterface
    $tokenStorage) :Response
    {
        if ($request->isMethod('POST')) {
            $appUser = $tokenStorage->getToken()->getUser();
            $arrParameters = array();
            $arrParameters['quicksearch'] = $request->get('quicksearch');
            $arrParameters['from'] = $request->get('from');
            $arrParameters['to'] = $request->get('to');
            $arrOwnEventData = $myAccountService->getOwnEventData($appUser, $this->getParameter('company_id'), $arrParameters);
            return $this->render('portal/my-account/event/_ajax-event-listing.html.twig', [
                'arrSocialProfileData' => $this->arrSocialProfileData, 'arrOwnEventData' => $arrOwnEventData['eventListing'],
                'totalEventCount' => $arrOwnEventData['totalEventCount'], 'arrEventUpComingOrOnGoingDetails' =>
                    $arrOwnEventData['arrEventUpComingOrOnGoingDetails']
            ]);
        }
    }

    /**
     * @Route("/my-account/ajax-view-event-participant-list/{id}", name="my-account-ajax-view-event-participant-list", methods={ "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TokenStorageInterface $tokenStorage
     * @param MyAccountService $myAccountService
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return Response
     */
    public function ajaxViewEventParticipantList(Request $request, TrnCircleEvents $trnCircleEvents, TokenStorageInterface $tokenStorage,
                                                 MyAccountService $myAccountService, TrnCircleEventsRepository $trnCircleEventsRepository) :Response
    {
        $mstProductType = $request->get('mstProductType');
        $mstStatus      = $request->get('mstStatus');
        $quicksearch    = $request->get('quicksearch');
        $arrParameters  = array();
        if (!empty($mstProductType)) {
            $arrParameters['mstProductType'] = $mstProductType;
        }
        if (!empty($mstStatus)) {
            $arrParameters['mstStatus'] = $mstStatus;
        }
        if (!empty($quicksearch)) {
            $arrParameters['quicksearch'] = $quicksearch;
        }

        $appUser = $tokenStorage->getToken()->getUser();
        $profileCompleteness = $myAccountService->getProfileCompleteness($appUser);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails([$trnCircleEvents], $entityManager);
        $arrProjectMemberListData = $myAccountService->getEventMemberList($trnCircleEvents, $arrParameters);
        return $this->render('portal/my-account/event/_ajax-view-event-participant-list.html.twig', [
            'appUser' => $appUser, 'profileCompleteness' => $profileCompleteness, 'eventData' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails, 'arrProjectMemberListData' => $arrProjectMemberListData
        ]);
    }

    /**
     * @Route("/my-account/update-is-trending/{id}", name="my-account-update-is-trending", methods={ "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return JsonResponse
     */
    public function updateIsTrending(Request $request, TrnCircleEvents $trnCircleEvents) :JsonResponse {
        $isChecked = $request->get('isChecked');
        $trnCircleEvents->setIsTrending($isChecked);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trnCircleEvents);
        $entityManager->flush();
        //$this->addFlash('success', 'Event Trending updated successfully.');
        if($isChecked)
            return new JsonResponse([ 'Message' => 'Event '.$trnCircleEvents->getName(). ' is now Trending']);
        else
            return new JsonResponse([ 'Message' => 'Event '.$trnCircleEvents->getName(). ' is removed from Trending']);
    }
}