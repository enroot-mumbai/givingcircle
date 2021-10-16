<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEventRequestToParticipate;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCircleRequestToJoin;
use App\Entity\Transaction\TrnMaterialReceivedAtCollectionCentre;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\Transaction\TrnCircleEventRequestToParticipateRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRequestToJoinRepository;
use App\Repository\Transaction\TrnMaterialInKindCircleEventCollectionCentreRepository;
use App\Repository\Transaction\TrnMaterialInKindCircleEventDetailsRepository;
use App\Repository\Transaction\TrnMaterialInKindCircleEventSubEventsRepository;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use App\Service\ProjectService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class MaterialController
 * @package App\Controller\Portal
 */
class MaterialController extends AbstractController
{
    /**
     * @Route("/portal/events/material-details/{id}", name="material-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param SessionInterface $session
     * @return void
     */
    public function materialDetails(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository
    $trnCircleEventsRepository, SessionInterface $session)
    {
        $trnMaterialInKindCircleEventDetails = null;
        $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
        if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
            $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
        }
        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);
        $session->set('redirectURLName', 'material-details');
        $session->set('redirectSubEventId', $trnCircleEvents->getId());

        if($session->has('nowJoined')) {
            $nowJoined = $session->get('nowJoined');
            $session->remove('nowJoined');
        } else {
            $nowJoined = false;
        }

        return $this->render('portal/material/material-event-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnMaterialInKindCircleEventDetails' =>
                $trnMaterialInKindCircleEventDetails, 'form' => $form->createView(),
            'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
            'changeMakerEmail' => $trnCircleEvents->getAppUser()->getAppUserInfo()->getUserEmail(),
            'nowJoined' => $nowJoined
        ]);
    }

    /**
     * @Route("/portal/events/material-details-contribute", name="material-details-contribute", methods={"POST"})
     * @IsGranted("ROLE_APP_USER")
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleRequestToJoinRepository $trnCircleRequestToJoinRepository
     * @param TrnMaterialInKindCircleEventDetailsRepository $trnMaterialInKindCircleEventDetailsRepository
     * @param TrnMaterialInKindCircleEventSubEventsRepository $trnMaterialInKindCircleEventSubEventsRepository
     * @param TrnMaterialInKindCircleEventCollectionCentreRepository $trnMaterialInKindCircleEventCollectionCentreRepository
     * @param TrnCircleEventRequestToParticipateRepository $trnCircleEventRequestToParticipateRepository
     * @param MyAccountService $myAccountService
     * @param ProjectService $projectService
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @return void
     */
    public function materialDetailsContribute(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository,
                  TokenStorageInterface $tokenStorage, TrnCircleRequestToJoinRepository $trnCircleRequestToJoinRepository,
                  TrnMaterialInKindCircleEventDetailsRepository $trnMaterialInKindCircleEventDetailsRepository,
                  TrnMaterialInKindCircleEventSubEventsRepository $trnMaterialInKindCircleEventSubEventsRepository,
                  TrnMaterialInKindCircleEventCollectionCentreRepository $trnMaterialInKindCircleEventCollectionCentreRepository,
                  TrnCircleEventRequestToParticipateRepository $trnCircleEventRequestToParticipateRepository,
                  MyAccountService $myAccountService, ProjectService $projectService, SessionInterface $session,
                                              NotificationService $notificationService
    )
    {
        if($request->isMethod('POST')) {

            $trnMaterialInKindCircleEventSubEventId	= $request->get('trnMaterialInKindCircleEventSubEventId');
            $itemQuantity							= $request->get('itemQuantity');
            $centerSelected							= $request->get('collectionCenter_'.$trnMaterialInKindCircleEventSubEventId);
            $trnEventId								= $request->get('trnEventId');
            $trnMaterialInKindCircleEventDetailsId	= $request->get('trnMaterialInKindCircleEventDetailsId');
            $bSendNotification                      = false;
            $appUser = $tokenStorage->getToken()->getUser();
            $TrnCircleEvents = $trnCircleEventsRepository->find($trnEventId);
            $TrnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsRepository->find($trnMaterialInKindCircleEventDetailsId);
            $TrnMaterialInKindCircleEventSubEvent =  $trnMaterialInKindCircleEventSubEventsRepository->find($trnMaterialInKindCircleEventSubEventId);
            $TrnMaterialInKindCircleEventCollectionCentre = $trnMaterialInKindCircleEventCollectionCentreRepository->find($centerSelected);
            $em = $this->getDoctrine()->getManager();

            $objTrnMaterialReceivedAtCollectionCentre = new TrnMaterialReceivedAtCollectionCentre();
            $objTrnMaterialReceivedAtCollectionCentre->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objTrnMaterialReceivedAtCollectionCentre->setIsActive(1);
            $objTrnMaterialReceivedAtCollectionCentre->setAppUser($appUser);
            $objTrnMaterialReceivedAtCollectionCentre->setTrnCircleEvents($TrnCircleEvents);
            $objTrnMaterialReceivedAtCollectionCentre->setTrnCollectionCentreDetails
            ($TrnMaterialInKindCircleEventCollectionCentre->getTrnCollectionCentreDetails());
            $objTrnMaterialReceivedAtCollectionCentre->setTrnMaterialInKindCircleEventDetails($TrnMaterialInKindCircleEventDetails);
            $objTrnMaterialReceivedAtCollectionCentre->setItemQuantity($itemQuantity);
            $objTrnMaterialReceivedAtCollectionCentre->setItemName($TrnMaterialInKindCircleEventSubEvent->getItemName());
            $objTrnMaterialReceivedAtCollectionCentre->setItemUnit($TrnMaterialInKindCircleEventSubEvent->getUnit());
            $objTrnMaterialReceivedAtCollectionCentre->setCreatedOn(new \DateTime());
            $objTrnMaterialReceivedAtCollectionCentre->setIsReceived(0);
            $em->persist($objTrnMaterialReceivedAtCollectionCentre);

            $trnCircleRequestToJoinExist = $trnCircleRequestToJoinRepository->findOneBy(array('trnCircle' =>
                $TrnCircleEvents->getTrnCircle(), 'appUser' => $appUser, 'isActive' => 1));
            $trnCircleEventRequestToParticipateExist = $trnCircleEventRequestToParticipateRepository->findOneBy(array
            ('trnCircleEvent' => $TrnCircleEvents, 'appUser' => $appUser, 'mstProductType' => $this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true, 'eventProductType' => 'Material (in Kind)'])));

            if (empty($trnCircleRequestToJoinExist)) {

                // make him join and throw to project list with appropriate msg
                if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open')
                    $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']);
                else
                    $objMstStatus = $this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Pending Activation']);

                $objOrgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
                $projectService->makeEntryForRequestToJoin($appUser, $TrnCircleEvents->getTrnCircle(), $objMstStatus, $objOrgCompany);
            }
            if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open')
                $myAccountService->makeEntryForCollectionCentre($appUser, $TrnCircleEvents->getTrnCircle(), $TrnCircleEvents);
            if (empty($trnCircleEventRequestToParticipateExist)) {
                $objTrnCircleRequestToJoin = new TrnCircleEventRequestToParticipate();
                $objTrnCircleRequestToJoin->setAppUser($appUser);
                $objTrnCircleRequestToJoin->setFirstName($appUser->getAppUserInfo()->getUserFirstName());
                $objTrnCircleRequestToJoin->setLastName($appUser->getAppUserInfo()->getUserLastName());
                $objTrnCircleRequestToJoin->setEmailAddress($appUser->getAppUserInfo()->getUserEmail());
                $objTrnCircleRequestToJoin->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $objTrnCircleRequestToJoin->setMstCity($appUser->getAppUserInfo()->getMstCity());
                
                $objTrnCircleRequestToJoin->setIsActive(1);
                $objTrnCircleRequestToJoin->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnCircleRequestToJoin->setTrnCircleEvent($TrnCircleEvents);
                $objTrnCircleRequestToJoin->setRequestedOn(new \DateTime());
                if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open') {
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']));
                } else {
                    if (!empty($trnCircleRequestToJoinExist) && $trnCircleRequestToJoinExist->getMstStatus()->getStatus
                    () == 'Activated'){
                        $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                        => 'Activated']));
                    } else {
                        $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                        => 'Pending Activation']));
                        $bSendNotification = true;
                    }
                }
                $objTrnCircleRequestToJoin->setMstProductType($this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true, 'eventProductType' => 'Material (in Kind)']));
                $em->persist($objTrnCircleRequestToJoin);
            }
            $em->flush();
            $session->set('disableback', true);

            if ($bSendNotification) {
                //Event Request to Participate Creator
                $notificationService->setAppUser($TrnCircleEvents->getAppUser());
                $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($TrnCircleEvents);
                $notificationService->setRequesterAppUser($appUser);
                $notificationService->doProcess('Project Request to Join Creator');

                //Event Request to Participate Requester
                $notificationService->setAppUser($appUser);
                $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($TrnCircleEvents);
                $notificationService->setRequesterAppUser($appUser);
                $notificationService->doProcess('Event Request to Participate Requester');
            }

            $notificationService->setAppUser($appUser);
            $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
            $notificationService->setTrnCircleEvents($TrnCircleEvents);
            $notificationService->setRequesterAppUser($appUser);
            $notificationService->doProcess('Material To Collection Centre');
            return $this->redirectToRoute('material-details-success');
        } else {
            return $this->redirectToRoute('event-listing');
        }

    }

    /**
     * @Route("/portal/events/material-details-success", name="material-details-success", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function materialContributeSuccess(Request $request, SessionInterface $session) :Response {
        $session->remove('redirectURLName');
        $session->remove('redirectSubEventId');

        if($session->has('disableback')) {
            $session->remove('disableback');
            return $this->redirectToRoute('material-details-success');
        }
        return $this->render('portal/material/material-details-success.html.twig', []);
    }
}