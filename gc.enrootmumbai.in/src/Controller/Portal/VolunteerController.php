<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEventRequestToParticipate;
use App\Entity\Transaction\TrnVolunteerCircleParticipationDetails;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCircleRequestToJoin;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use App\Entity\Transaction\TrnOrder;
use App\Form\Transaction\TrnUserCommentsType;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventRequestToParticipateRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnCircleRequestToJoinRepository;
use App\Repository\Transaction\TrnVolunterCircleEventDetailsRepository;
use App\Repository\Transaction\TrnVolunterCircleEventSubEventsRepository;
use App\Service\MyAccountService;
use App\Service\NotificationService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class VolunteerController
 * @package App\Controller\Portal
 */
class VolunteerController extends AbstractController
{
    /**
     * @Route("/portal/events/volunteer-details/{id}", name="volunteer-details", methods={"GET", "POST"})
     * @param Request $request
     * @param TrnCircleEvents $trnCircleEvents
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @return void
     * @throws \Exception
     */
    public function volunteerDetails(Request $request, TrnCircleEvents $trnCircleEvents, TrnCircleEventsRepository
    $trnCircleEventsRepository, SessionInterface $session)
    {
        $trnVolunteerCircleEventDetails = null;
        $trnVolunteerCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();
        if (!empty($trnVolunteerCircleEventDetailsArray) && !empty($trnVolunteerCircleEventDetailsArray[0])) {
            $trnVolunteerCircleEventDetails = $trnVolunteerCircleEventDetailsArray[0];
        }
        $trnVolunteerCircleEventDetails_recurringDetails = array();
        $recurringDetailsArray = $trnCircleEvents->getTrnCircleEventRecurringDetails();
        if(!empty($recurringDetailsArray) && !empty($recurringDetailsArray[0])){
            $trnVolunteerCircleEventDetails_recurringDetails = $recurringDetailsArray[0];
        }
        $begin = new DateTime( $trnVolunteerCircleEventDetails->getFromDate()->format('Y-m-d') );
        $end = new DateTime( $trnVolunteerCircleEventDetails->getToDate()->format('Y-m-d') );
        $end = $end->modify( '+1 day' );
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval ,$end);
        $arrEventDates = array();
        if ($trnVolunteerCircleEventDetails->getMstEventOccurrence() == 'Recurring') {
            $strRecurringBy = $trnVolunteerCircleEventDetails_recurringDetails->getMstRecurringBy()
                ->getRecurringBy();
            switch (strtolower($strRecurringBy)) {
                case 'daily': {
                    foreach($dateRange as $date){
                        $arrEventDates[] = $date->format("m/d/Y");
                    }
                    break;
                }
                case 'weekly': {
                    $mstDaysOfWeekArr = $trnVolunteerCircleEventDetails_recurringDetails->getMstDaysOfWeek();
                    $arrWeekDays = array();
                    foreach ($mstDaysOfWeekArr as $mstDaysOfWeek) {
                        $arrWeekDays[] = $mstDaysOfWeek->getDayOfWeek();
                    }
                    foreach($dateRange as $date){
                        if (in_array($date->format('l'), $arrWeekDays)){
                            $arrEventDates[] = $date->format("m/d/Y");
                        }
                    }
                    break;
                }
                case 'monthly': {
                    $datesSelected = $trnVolunteerCircleEventDetails_recurringDetails->getDatesSelected();
                    $tempArr = explode(',', $datesSelected);
                    foreach ($tempArr as $data) {
                        $tmpDate = new \DateTime(trim($data));
                        $arrDateSelected[] =  $tmpDate->format('d');
                    }
                    foreach($dateRange as $date){
                        if (in_array($date->format('d'), $arrDateSelected)){
                            $arrEventDates[] = $date->format("m/d/Y");
                        }
                    }
                    break;
                }
            }
        } else {
            foreach($dateRange as $date){
                $arrEventDates[] = $date->format("m/d/Y");
            }
        }
        $trnCircleEventComments = new TrnCircleEventComments();
        $form = $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments);
        $form->handleRequest($request);
        $session->set('redirectURLName', 'volunteer-details');
        $session->set('redirectSubEventId', $trnCircleEvents->getId());

        if($session->has('nowJoined')) {
            $nowJoined = $session->get('nowJoined');
            $session->remove('nowJoined');
        } else {
            $nowJoined = false;
        }

        return $this->render('portal/volunteer/volunteer-event-details.html.twig', [
            'trnCircleEvents' => $trnCircleEvents, 'trnVolunteerCircleEventDetails' => $trnVolunteerCircleEventDetails,
            'trnVolunteerCircleEventDetails_recurringDetails' => $trnVolunteerCircleEventDetails_recurringDetails,
            'arrEventDates' => $arrEventDates, 'form' => $form->createView(),
            'reply_form' => $this->createForm(TrnUserCommentsType::class, $trnCircleEventComments),
            'changeMakerEmail' => $trnCircleEvents->getAppUser()->getAppUserInfo()->getUserEmail(), 'nowJoined' => $nowJoined
        ]);
    }

    /**
     * @Route("/portal/events/volunteer-request-participate", name="volunteer-request-participate", methods={"POST"})
     * @IsGranted("ROLE_APP_USER")
     * @param Request $request
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param TrnVolunterCircleEventDetailsRepository $trnVolunterCircleEventDetailsRepository
     * @param TrnVolunterCircleEventSubEventsRepository $trnVolunterCircleEventSubEventsRepository
     * @param TokenStorageInterface $tokenStorage
     * @param TrnCircleRequestToJoinRepository $trnCircleRequestToJoinRepository
     * @param TrnCircleEventRequestToParticipateRepository $trnCircleEventRequestToParticipateRepository
     * @param MyAccountService $myAccountService
     * @param SessionInterface $session
     * @param NotificationService $notificationService
     * @return void
     * @throws \Exception
     */
    public function volunteerRequestToParticipate(Request $request, TrnCircleEventsRepository $trnCircleEventsRepository,
                                                  TrnVolunterCircleEventDetailsRepository $trnVolunterCircleEventDetailsRepository,
                                                  TrnVolunterCircleEventSubEventsRepository
                                                  $trnVolunterCircleEventSubEventsRepository, TokenStorageInterface
                                                  $tokenStorage, TrnCircleRequestToJoinRepository
                                                  $trnCircleRequestToJoinRepository,
                                                  TrnCircleEventRequestToParticipateRepository
                                                  $trnCircleEventRequestToParticipateRepository,
                                                  MyAccountService $myAccountService, SessionInterface $session,
                                                  NotificationService $notificationService)
    {
        if($request->isMethod('POST')) {
            $numberOfHours = $request->get('numberOfHours');
            $trnEventId = $request->get('trnEventId');
            $trnVolunteerCircleEventDetailsId = $request->get('trnVolunteerCircleEventDetailsId');
            $trnVolunteerCircleEventSubEventsId = $request->get('trnVolunterCircleEventSubEventsId');
            $selectedDateRange = $request->get('selectedDateRange');
            $eventTime = $request->get('eventTime');
            $appUser = $tokenStorage->getToken()->getUser();
            $TrnCircleEvents = $trnCircleEventsRepository->find($trnEventId);
            $TrnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsRepository->find($trnVolunteerCircleEventDetailsId);
            $TrnVolunterCircleEventSubEvents = $trnVolunterCircleEventSubEventsRepository->find($trnVolunteerCircleEventSubEventsId);
            $em = $this->getDoctrine()->getManager();
            $arrSelectedDateRange = explode(',', $selectedDateRange);
            $bSendNotification = $bSendCircleNotification = false;
            foreach ($arrSelectedDateRange as $strSelectedDate) {
                $objTrnVolunteerCircleParticipationDetails = new TrnVolunteerCircleParticipationDetails();
                $objTrnVolunteerCircleParticipationDetails->setTrnVolunteerCircleEventDetail($TrnVolunterCircleEventDetails);
                $objTrnVolunteerCircleParticipationDetails->setTrnCircleEvent($TrnCircleEvents);
                $objTrnVolunteerCircleParticipationDetails->setTrnVolunterCircleEventSubEvent($TrnVolunterCircleEventSubEvents);
                $objTrnVolunteerCircleParticipationDetails->setDateOfService(new \DateTime($strSelectedDate));
                $objTrnVolunteerCircleParticipationDetails->setFromTime(new \DateTime("0000-00-00 $eventTime:00:00"));
                $objTrnVolunteerCircleParticipationDetails->setNumberOfHours($numberOfHours);
                $objTrnVolunteerCircleParticipationDetails->setIsActive(1);
                $objTrnVolunteerCircleParticipationDetails->setAppUser($appUser);
                $em->persist($objTrnVolunteerCircleParticipationDetails);
            }

            //First Check if Entry in Circle and Request Join List
            $trnCircleRequestToJoinExist = $trnCircleRequestToJoinRepository->findOneBy(array('trnCircle' =>
                $TrnCircleEvents->getTrnCircle(), 'appUser' => $appUser, 'isActive' => 1));
            $trnCircleEventRequestToParticipateExist = $trnCircleEventRequestToParticipateRepository->findOneBy(array
            ('trnCircleEvent' => $TrnCircleEvents, 'appUser' => $appUser, 'mstProductType' => $this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true, 'eventProductType' => 'Volunteer (in Time)'])));

            if (empty($trnCircleRequestToJoinExist)) {
                $objTrnCircleRequestToJoin = new TrnCircleRequestToJoin();
                $objTrnCircleRequestToJoin->setAppUser($appUser);
                if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open')
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']));
                else
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Pending Activation']));
                $objTrnCircleRequestToJoin->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnCircleRequestToJoin->setIsActive(1);
                $objTrnCircleRequestToJoin->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $objTrnCircleRequestToJoin->setRequestOn(new \DateTime());
                $objTrnCircleRequestToJoin->setTrnCircle($TrnCircleEvents->getTrnCircle());
                $em->persist($objTrnCircleRequestToJoin);
                $bSendCircleNotification = true;
            }
            if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open')
                $myAccountService->makeEntryForCollectionCentre($appUser, $TrnCircleEvents->getTrnCircle(), $TrnCircleEvents);
            if (empty($trnCircleEventRequestToParticipateExist)) {
                $objTrnCircleRequestToJoin = new TrnCircleEventRequestToParticipate();
                $objTrnCircleRequestToJoin->setAppUser($appUser);
                $objTrnCircleRequestToJoin->setFirstName($appUser->getAppUserInfo()->getUserFirstName());
                $objTrnCircleRequestToJoin->setLastName($appUser->getAppUserInfo()->getUserLastName());
                $objTrnCircleRequestToJoin->setEmailAddress($appUser->getAppUserInfo()->getUserEmail());
                $objTrnCircleRequestToJoin->setEmailAddress($appUser->getAppUserInfo()->getUserEmail());
                $objTrnCircleRequestToJoin->setMobileCountryCode($appUser->getAppUserInfo()->getMobileCountryCode());
                $objTrnCircleRequestToJoin->setMobileNumber($appUser->getAppUserInfo()->getUserMobileNumber());
                $objTrnCircleRequestToJoin->setMstCity($appUser->getAppUserInfo()->getMstCity());
//                $objTrnCircleRequestToJoin->setMstCity($appUser->getAppUserInfo()->getMstCity());
                $objTrnCircleRequestToJoin->setIsActive(1);
                $objTrnCircleRequestToJoin->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $objTrnCircleRequestToJoin->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnCircleRequestToJoin->setTrnCircleEvent($TrnCircleEvents);
                $objTrnCircleRequestToJoin->setRequestedOn(new \DateTime());
                if ($TrnCircleEvents->getTrnCircle()->getMstJoinBy()->getJoinBy() == 'Open') {
                    $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                    => 'Activated']));
                } else {
                    if (!empty($trnCircleRequestToJoinExist) && $trnCircleRequestToJoinExist->getMstStatus()->getStatus() == 'Activated'){
                        $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository
                        (MstStatus::class)->findOneBy(["status" => 'Activated']));
                    } else {
                        $objTrnCircleRequestToJoin->setMstStatus($this->getDoctrine()->getRepository(MstStatus::class)->findOneBy(["status"
                        => 'Pending Activation']));
                        $bSendNotification = true;
                    }
                }
                $objTrnCircleRequestToJoin->setMstProductType($this->getDoctrine()->getRepository(MstEventProductType::class)->findOneBy(["isActive" => 1, 'eventProductType' => 'Volunteer (in Time)']));
                $em->persist($objTrnCircleRequestToJoin);
            }
            $em->flush();
            $session->set('disableback', true);

            if ($bSendCircleNotification) {
                //Project Request to Join Creator
                $notificationService->setAppUser($TrnCircleEvents->getTrnCircle()->getAppUser());
                $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
                $notificationService->setRequesterAppUser($appUser);
                $notificationService->doProcess('Project Request to Join Creator');

                //Project Request to Join Member
                $notificationService->setAppUser($appUser);
                $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
                $notificationService->setRequesterAppUser($appUser);
                $notificationService->doProcess('Project Request to Join Member');
            }

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

            //Volunteer Nomination Creator
            $notificationService->setAppUser($TrnCircleEvents->getAppUser());
            $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
            $notificationService->setTrnCircleEvents($TrnCircleEvents);
            $notificationService->setRequesterAppUser($appUser);
            $notificationService->doProcess('Volunteer Nomination Creator');

            //Volunteer Nomination Participant
            $notificationService->setAppUser($appUser);
            $notificationService->setTrnCircle($TrnCircleEvents->getTrnCircle());
            $notificationService->setTrnCircleEvents($TrnCircleEvents);
            $notificationService->setRequesterAppUser($appUser);
            $notificationService->doProcess('Volunteer Nomination Participant');
            return $this->redirectToRoute('volunteer-participate-success');
        } else {
            return $this->redirectToRoute('event-listing');
        }
    }

    /**
     * @Route("/portal/events/volunteer-participate-success", name="volunteer-participate-success", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function volunteerParticipateSuccess(Request $request, SessionInterface $session) :Response
    {
        $session->remove('redirectURLName');
        $session->remove('redirectSubEventId');
        if($session->has('disableback')) {
            $session->remove('disableback');
            return $this->redirectToRoute('volunteer-participate-success');
        }
        return $this->render('portal/volunteer/volunteer-participate-success.html.twig', []);
    }
}