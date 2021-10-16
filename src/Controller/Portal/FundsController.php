<?php

namespace App\Controller\Portal;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsPage;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use App\Entity\Transaction\TrnOrder;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Service\ProjectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FundsController extends AbstractController
{
    /**
     * @Route("/portal/funds/express-donate-listing", name="express-donate-listing", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @return Response
     */
    public function expressDonateListing(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository,
                                         SessionInterface $session) {
//        $areaInterests = $this->getDoctrine()->getRepository(CmsArticle::class)->getAreaInterestList(2, $this->getParameter('company_id'));
        //$areaInterests = $this->getDoctrine()->getRepository(MstAreaInterest::class)->findBy(['isActive' => 1, 'mstAreaInterestPrimary' => null], ['sequenceNo' => 'ASC']);
        $areaInterests = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->getPrimaryAreaInterestList($this->getParameter('company_id'));
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(["isActive" => true,
            'eventProductType' => array ('Fundraiser','Crowdfunding')]);
        $arrTemp = array();
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            $arrTemp[] = $MstEventProductType->getId();
        }
        $arraySearch = array();
        $session->set('expressDonate', 1);
        $arrEventList =  $trnCircleEventsRepository->getFundRaiserAndCrowdFundingEvents($objMstStatus, $this->getParameter('company_id'), $arrTemp, $arraySearch, $expiredObjMstStatus);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        return $this->render('portal/funds/express-donate-listing.html.twig', [
            'areaInterests' => $areaInterests, 'arrEventList' => $arrEventList, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
    }

    /**
     * @Route("/portal/funds/ajax-express-donate-listing", name="ajax-express-donate-listing", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return JsonResponse
     */
    public function ajaxExpressDonateListing(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository) {
        $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $arrInputParam = array();
        $arrProductType = array ('Fundraiser','Crowdfunding');
        if ($request->isMethod('POST')) {$arrInputParam = array();
            if (!empty($request->get("categoryChkBox"))) {
                $arrProductType = array();
                $arrInputParam['categoryChkBox'] = $request->get("categoryChkBox");
                foreach ($arrInputParam['categoryChkBox'] as $category) {
                    if ($category == 'Crowdfunding') {
                        $arrProductType[] = 'Crowdfunding';
                    } else {
                        $arrProductType[] = 'Fundraiser';
                    }
                }
            }
            $arrInputParam['statusChkBox'] = $request->get("statusChkBox");
            $arrInputParam['areaOfInterestChkBox'] = $request->get("areaOfInterestChkBox");
            $arrInputParam['searchCity'] = $request->get("searchCity");
            $arrInputParam['searchText'] = $request->get("searchText");
        }
        $arrMstEventProductTypeObj = $this->getDoctrine()->getRepository(MstEventProductType::class)->findBy(["isActive" => true,
            'eventProductType' => $arrProductType]);
        $arrTemp = array();
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            $arrTemp[] = $MstEventProductType->getId();
        }
        $arrEventList =  $trnCircleEventsRepository->getFundRaiserAndCrowdFundingEvents($objMstStatus, $this->getParameter('company_id'),
            $arrTemp, $arrInputParam, $expiredObjMstStatus);
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        $response = $this->render('portal/funds/_ajax-express-donate-listing.html.twig', [
            'arrEventList' => $arrEventList, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails
        ]);
        return new JsonResponse([ 'html' => $response->getContent(), 'count' => count($arrEventList)]);
    }

    /**
     * @Route("/portal/funds/fund-details/{id}", name="fund-details", methods={"GET","POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @param ProjectService $projectService
     * @return Response
     */
    public function fundDetails(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository
    $trnCircleEventsRepository, SessionInterface $session, ProjectService $projectService) {
        $arrEventList[] = $trnCircleEvents;
        $entityManager = $this->getDoctrine()->getManager();
        $arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $entityManager);
        $trnFundRaiserCircleEventDetails = null;
        $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
        if (!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])) {
            $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
        }
        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);
        $session->set('redirectURLName', 'fund-details');
        $session->set('redirectSubEventId', $trnCircleEvents->getId());

        if($session->has('nowJoined')) {
            $nowJoined = $session->get('nowJoined');
            $session->remove('nowJoined');
        } else {
            $nowJoined = false;
        }
        $projectService->makeEntryForVisitor($trnCircleEvents->getTrnCircle(), $trnCircleEvents);
        return $this->render('portal/funds/fund-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'arrEventUpComingOrOnGoingDetails' =>
                $arrEventUpComingOrOnGoingDetails, 'trnFundRaiserCircleEventDetails' =>
                $trnFundRaiserCircleEventDetails, 'form' => $form->createView(),
                'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
                'changeMakerEmail' => $trnCircleEvents->getAppUser()->getAppUserInfo()->getUserEmail(),
                'nowJoined' => $nowJoined
        ]);
    }

    /**
     * @Route("/portal/funds/share-your-details", name="share-your-details", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param AppUserRepository $appUserRepository
     * @return void
     */
    public function shareYourDetails(Request $request, SessionInterface $session, TokenStorageInterface $tokenStorage
        , AppUserRepository $appUserRepository) {
        $amountToContribute = $session->get('amountToContribute', 0);
        $fundRaiserEventId = $session->get('fundRaiserEventId', null);
        $subEventId = $session->get('subEventId', null);
        $eventId = $session->get('eventId', null);
        $changemakerarticleId = $session->get('changemakerarticleId', null);

        if (empty($amountToContribute) || (empty($eventId) && empty($fundRaiserEventId)) || empty($eventId)) {

            // if all other things are empty and changemakerarticleId is also empty then redirect to listing
            if(empty($changemakerarticleId)) {
                return $this->redirectToRoute('express-donate-listing');
            }
        }

        $user = $tokenStorage->getToken()->getUser();
        $arrUserData = array();
        $arrUserData['first_name']    = '';
        $arrUserData['last_name']     = '';
        $arrUserData['email']         = '';
        $arrUserData['mobile_number'] = '';
        $arrUserData['pan_number']    = '';

        if ( is_object($user) && get_class($user) == AppUser::class) {
            $appUser = $user;
            if (!empty($appUser)) {
                $arrUserData['first_name']    = $appUser->getAppUserInfo()->getUserFirstName();
                $arrUserData['last_name']     = $appUser->getAppUserInfo()->getUserLastName();
                $arrUserData['email']         = $appUser->getAppUserInfo()->getUserEmail();
                $arrUserData['mobile_number'] = $appUser->getAppUserInfo()->getUserMobileNumber();
                $arrUserData['pan_number']    = $appUser->getAppUserInfo()->getPancardNumber();

            }
        }

        $strImage = "";
        $trnCircleEvent = null;
        $trnFundRaiserCircleEventSubEvent = null;
        $trnCrowdFundEvents = null;
        $changeMakerArticle = null;

        if(!empty($changemakerarticleId)) {

            // if $changemakerarticleId is not empty - the donation is for GC, so no event or project
            $changeMakerArticle = $this->getDoctrine()->getRepository(CmsArticle::class)->find($changemakerarticleId);

        } else {
            $trnCircleEvent = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->find($eventId);

            if (!empty($subEventId))
                $trnFundRaiserCircleEventSubEvent = $this->getDoctrine()->getRepository(TrnFundRaiserCircleEventSubEvents::class)->find($subEventId);
            elseif (!empty($fundRaiserEventId))
                $trnCrowdFundEvents = $this->getDoctrine()->getRepository(TrnCrowdFundEvent::class)->find($fundRaiserEventId);

            if (empty($trnCircleEvent) && empty($trnCrowdFundEvents) )
                return $this->redirectToRoute('express-donate-listing');

            foreach ($trnCircleEvent->getTrnProductMedia() as $trnProductMedia) {
                if (  strtolower($trnProductMedia->getMediaType()) == 'image' && $trnProductMedia->getMediaFileName()) {
                    $strImage =  $trnProductMedia->getuploadedFilePath();
                    break;
                }
            }
        }

        $tncContent = $this->getDoctrine()->getRepository(CmsPage::class)->findOneBy(['pageRoute' => 'terms-and-conditions', 'orgCompany' => $this->getParameter('company_id')]);

        return $this->render('portal/funds/share-your-details.html.twig', [ 'amountToContribute' =>
            $amountToContribute, 'arrUserData'=> $arrUserData, 'trnCircleEvent' => $trnCircleEvent,
            'trnFundRaiserCircleEventSubEvent' => $trnFundRaiserCircleEventSubEvent, 'tncContent' => $tncContent,
            'tnCrowdFundEvent' => $trnCrowdFundEvents, 'strImage' => $strImage, 'changeMakerArticle' => $changeMakerArticle
        ]);
    }

    /**
     * @Route("/portal/funds/save-sub-event", name="save-sub-event", methods={"GET","POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @return void
     */
    public function SaveSubEvent(Request $request, SessionInterface $session, TokenStorageInterface $tokenStorage) {
        $expressDonate =  $session->get('expressDonate', 0);
        if (empty($expressDonate)) {
            $user = $tokenStorage->getToken()->getUser();
            if ( is_object($user) && get_class($user) == AppUser::class) { } else {
                return $this->redirectToRoute('login-email');
            }
        }
        $session->remove('redirectURLName');
        $session->remove('redirectSubEventId');

        $amountToContribute = $request->get('amountToContribute');
        $subEventId = $request->get('subEventId');
        $fundRaiserEventId = $request->get('fundRaiserEventId');
        $eventId = $request->get('eventId');
        $session->set('amountToContribute', $amountToContribute);
        if (!empty($subEventId))
            $session->set('subEventId', $subEventId);
        elseif (!empty($fundRaiserEventId))
            $session->set('fundRaiserEventId', $fundRaiserEventId);
        $session->set('eventId', $eventId);
        return $this->redirectToRoute('share-your-details');
    }
}