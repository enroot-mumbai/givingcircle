<?php

namespace App\Controller\Portal;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
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
use App\Form\Transaction\TrnCircleEventsPortalType;
use App\Form\Transaction\TrnFundRaiserCircleEventDetailsPortalType;
use App\Form\Transaction\TrnMaterialInKindCircleEventDetailsPortalType;
use App\Form\Transaction\TrnUserCommentsType;
use App\Form\Transaction\TrnVolunterCircleEventDetailsPortalType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\FileUploaderHelper;
use App\Service\ProjectService;
use DateTime;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Service\CommonHelper;
use Ramsey\Uuid\Uuid;

/**
 * Class EventDetailsController
 */
class EventDetailsController extends AbstractController
{

    /**
     * @Route("/portal/events/event-details/{id}", name="event-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @param ProjectService $projectService
     * @return Response
     */
    public function eventDetails(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository
    $trnCircleEventsRepository, SessionInterface $session, ProjectService $projectService) {
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);
        $arrTrnAreaOfInterests =  $trnCircleEvents->getTrnAreaOfInterests();
        $arrPrimaryAI = $arrPrimaryAISecAI = array();
        $session->remove('expressDonate');
        $session->remove('redirectURLName');
        $session->remove('redirectSubEventId');
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            $arrPrimaryAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()] =
                $TrnAreaOfInterest->getAreaInterestPrimary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[$TrnAreaOfInterest->getAreaInterestPrimary()->getId()][] =
                    $areaInterest->getAreaInterest();
            }
        }

        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);
        $ReadCount = $trnCircleEvents->getReadCount();
        if(empty($ReadCount))
            $ReadCount =0 ;
        $ReadCount++;
        $trnCircleEvents->setReadCount($ReadCount);
        $entityManager->persist($trnCircleEvents);
        $entityManager->flush();
        //$projectService->makeEntryForVisitor($trnCircleEvents->getTrnCircle(), $trnCircleEvents);
        return $this->render('portal/event/event-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrPrimaryAISecAI' => $arrPrimaryAISecAI,
            'arrPrimaryAI' => $arrPrimaryAI,
            'form' => $form->createView(),
            'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
        ]);
    }

    /**
     * @Route("/portal/events/comment-section", name="comment-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function commentSection(Request $request, TrnCircleEvents $trnCircleEvents) {
        return $this->render('portal/event/comment-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'events' => $trnCircleEvents
        ]);
    }

    /**
     * @Route("/portal/events/event-listing", name="event-listing", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function eventListing(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository, SessionInterface $session) :Response
    {
        $srchArray = array();
        $arrTemp = array();
        /*if(isset($request) && !empty($request->get('project_name'))){
            $srchArray['searchText'] = $request->get('project_name');
        }*/
        if($session->has('project_id')) {
            // used to get project related events, this will be set from project list and detail page

            $circleId = $session->get('project_id');
            $srchArray['circleid'] = $circleId;
            $session->remove('project_id');
        }

        if($session->has('event_product_type')) {
            // used to get event product type related events, this will be set from volunteer diaries page

            $eventProductType = $session->get('event_product_type');
            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(["isActive" => true,
                'eventProductType' => $eventProductType]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                $arrTemp[] = $MstEventProductType->getId();
            }
            $session->remove('event_product_type');
        }

        if(isset($request) && !empty($request->get('user'))){
            $srchArray['userid'] = $request->get('user');
        }
        $areaInterests = $this->getDoctrine()->getRepository(MstAreaInterest::class)->findBy(['isActive' => 1, 'mstAreaInterestPrimary' => null], ['sequenceNo' => 'ASC']);
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);

        if(count($arrTemp) == 0 ){
            $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy
            (["isActive" => true]);
            foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
                if ($MstEventProductType->getEventProductType() == 'Crowdfunding')
                    continue;
                $arrTemp[] = $MstEventProductType->getId();
            }
        }

        $arrEventList =  $trnCircleEventsRepository->getFundRaiserAndCrowdFundingEvents($objMstStatus, $this->getParameter('company_id'), $arrTemp, $srchArray, $expiredObjMstStatus);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        return $this->render('portal/event/event-listing.html.twig', [
            'areaInterests' => $areaInterests, 'arrEventList' => $arrEventList,
            'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
    }

    /**
     * @Route("/portal/events/ajax-event-listing", name="ajax-event-listing", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return JsonResponse
     */
    public function ajaxEventListing(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository) {
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $arrProductType = $arrInputParam = array();
        $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(["isActive" => true]);
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if ($MstEventProductType->getEventProductType() == 'Crowdfunding')
                continue;
            $arrProductType[] = $MstEventProductType->getId();
        }
        if ($request->isMethod('POST')) {
            $arrInputParam = array();
            if (!empty($request->get("categoryChkBox"))) {
                $arrProductType = array();
                $arrInputParam['categoryChkBox'] = $request->get("categoryChkBox");
                foreach ($arrInputParam['categoryChkBox'] as $category) {
                    switch ($category){
                        case 'Volunteer':{
                            $arrProductType[] = "Volunteer (in Time)";
                            break;
                        }
                        case 'Material':{
                            $arrProductType[] = "Material (in Kind)";
                            break;
                        }
                        case 'Funds':{
                            $arrProductType[] = "Fundraiser";
                            break;
                        }
                    }
                }
            }
            $arrInputParam['eventTime'] = $request->get("eventTime");
            $arrTemp = $request->get("joinBy");
            $arrJoinBy = array();
            if (!empty($arrTemp)) {
                foreach ($arrTemp as $strJoinBy) {
                    $objMstJoinBy =  $this->getDoctrine()->getRepository(MstJoinBy::class)->findOneBy(["joinBy" => $strJoinBy]);
                    if (!empty($objMstJoinBy))
                        $arrJoinBy[] = $objMstJoinBy->getId();
                }
            }
            $arrInputParam['joinBy'] = $arrJoinBy;
            $arrInputParam['areaOfInterestChkBox'] = $request->get("areaOfInterestChkBox");
            $arrInputParam['searchCity'] = $request->get("searchText");
            $arrInputParam['searchText'] = $request->get("searchText");
        }
        $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->getSelectedProductType($arrProductType);
        $arrTemp = array();
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if ($MstEventProductType->getEventProductType() == 'Crowdfunding')
                continue;
            $arrTemp[] = $MstEventProductType->getId();
        }
        $arrEventList =  $trnCircleEventsRepository->getFundRaiserAndCrowdFundingEvents($objMstStatus, $this->getParameter('company_id'),
            $arrTemp, $arrInputParam, $expiredObjMstStatus);

        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        $response = $this->render('portal/event/_ajax-event-listing.html.twig', [
            'arrEventList' => $arrEventList, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
        return new JsonResponse([ 'html' => $response->getContent(), 'count' => count($arrEventList)]);
    }

    /**
     * @Route("/portal/events/event-details-banner", name="event-details-banner", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function eventDetailsBanner(Request $request, TrnCircleEvents $trnCircleEvents)
    {
        return $this->render('portal/event/event-details-banner.html.twig', [
            'trnCircleEvents' => $trnCircleEvents
        ]);
    }
    /**
     * @Route("/portal/events/event-header-section", name="event-header-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function eventDetailsHeaderSection(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository $trnCircleEventsRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
        ([$trnCircleEvents], $entityManager);
        return $this->render('portal/event/event-header-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' =>
            $arrEventUpComingOrOnGoingDetails
        ]);
    }

    /**
     * @Route("/portal/events/event-header-navigation-section", name="event-header-navigation-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param string $active
     * @return Response
     */
    public function eventDetailsNavigationSection(Request $request, TrnCircleEvents $trnCircleEvents,
                                                  SessionInterface $session, $active = 'event')
    {
        $expressDonate =  $session->get('expressDonate', 0);
        return $this->render('portal/event/event-header-navigation-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'active' => $active, 'expressDonate' => $expressDonate
        ]);
    }

    /**
     * @Route("/portal/events/event-bottom-navigation-section", name="event-bottom-navigation-section", methods={"GET"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @return Response
     */
    public function eventDetailsBottomNavigationSection(Request $request, TrnCircleEvents $trnCircleEvents, $active = 'event')
    {
        return $this->render('portal/event/event-bottom-navigation-section.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'active' => $active
        ]);
    }
}