<?php

namespace App\Service;

use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCircleEventsVisitors;
use App\Entity\Transaction\TrnCircleRequestToJoin;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Entity\Transaction\TrnOrder;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleEventsVisitorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class ProjectService
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var AppUserRepository
     */
    private $appUserRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var TrnCircleEventsVisitorsRepository
     */
    private $trnCircleEventsVisitorsRepository;

    /**
     * @param EntityManagerInterface $em
     * @param NotificationService $notificationService
     * @param AppUserRepository $appUserRepository
     * @param Security $security
     * @param TrnCircleEventsVisitorsRepository $trnCircleEventsVisitorsRepository
     */
    public function __construct(EntityManagerInterface $em, NotificationService $notificationService,
                                AppUserRepository $appUserRepository, Security $security,
                                TrnCircleEventsVisitorsRepository $trnCircleEventsVisitorsRepository) {
        $this->em = $em;
        $this->notificationService = $notificationService;
        $this->appUserRepository = $appUserRepository;
        $this->security = $security;
        $this->trnCircleEventsVisitorsRepository = $trnCircleEventsVisitorsRepository;
    }

    /**
     * @param AppUser $appUser
     * @param TrnCircle $objCircle
     * @param MstStatus $objMstStatus
     * @param OrgCompany $objOrgCompany
     * @return string[]
     */
    public function makeEntryForRequestToJoin(AppUser $appUser, TrnCircle $objCircle,
                                              MstStatus $objMstStatus, OrgCompany $objOrgCompany) :array
    {
        $appUserInfo = $appUser->getAppUserInfo();

        $objTrnCircleRequestToJoin = new TrnCircleRequestToJoin();
        $objTrnCircleRequestToJoin->setAppUser($appUser);
        $objTrnCircleRequestToJoin->setMstStatus($objMstStatus);
        $objTrnCircleRequestToJoin->setFirstName($appUserInfo->getUserFirstName());
        $objTrnCircleRequestToJoin->setLastName($appUserInfo->getUserLastName());
        $objTrnCircleRequestToJoin->setEmailAddress($appUserInfo->getUserEmail());
        $objTrnCircleRequestToJoin->setMobileCountryCode($appUserInfo->getMobileCountryCode());
        $objTrnCircleRequestToJoin->setMobileNumber($appUserInfo->getUserMobileNumber());
        $objTrnCircleRequestToJoin->setMstCity($appUserInfo->getMstCity());
        $objTrnCircleRequestToJoin->setOrgCompany($objOrgCompany);
        $objTrnCircleRequestToJoin->setIsActive(1);
        $objTrnCircleRequestToJoin->setUserIpAddress($_SERVER['SERVER_ADDR']);
        $objTrnCircleRequestToJoin->setRequestOn(new \DateTime());
        $objTrnCircleRequestToJoin->setTrnCircle($objCircle);
        $this->em->persist($objTrnCircleRequestToJoin);
        $this->em->flush();

        if ($objMstStatus->getStatus() == 'Pending Activation') {
            //Project Request to Join Creator
            $notificationService = $this->notificationService;
            $notificationService->setAppUser($objCircle->getAppUser());
            $notificationService->setTrnCircle($objCircle);
            $notificationService->setRequesterAppUser($appUser);
            $notificationService->doProcess('Project Request to Join Creator');

            //Project Request to Join Member
            $notificationService->setAppUser($appUser);
            $notificationService->setTrnCircle($objCircle);
            $notificationService->setRequesterAppUser($appUser);
            $notificationService->doProcess('Project Request to Join Member');
        }

        return array('entry' => 'made');
    }

    /**
     * @param TrnCircle $objTrnCircle
     * @param MstStatus $objMstStatus
     */
    public function changeProjectEventStatus(TrnCircle $objTrnCircle, MstStatus $objMstStatus) {

        foreach ($objTrnCircle->getTrnCircleEvents() as $objEvent) {

            if($objEvent->getMstStatus()->getStatus() == 'Activated' ||
                $objEvent->getMstStatus()->getStatus() == 'Pending Activation') {

                //$eventToChange = $this->em->getRepository(TrnCircleEvents::class)->findOneBy(array('id' => $objEvent->getId()));
                $objEvent->setMstStatus($objMstStatus);
                $this->em->persist($objEvent);
                $this->em->flush();

            }
        }
    }

    /**
     * @param TrnCircle $trnCircle
     */
    public function sendNotificationToAllUser(TrnCircle $trnCircle) {
        $arrAppUser = $this->appUserRepository->getApplicationUser();
        $strNotification = 'Project Open';
        if($trnCircle->getMstJoinBy() == 'Open'){
            $notificationService = $this->notificationService;
            foreach ($arrAppUser as $appUser) {
                $notificationService->setAppUser($appUser);
                $notificationService->setTrnCircle($trnCircle);
                $notificationService->doProcess('Project Open');
            }
        }
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     */
    public function sendEventNotificationToAllUser(TrnCircleEvents $trnCircleEvents) {
        $arrAppUser = $this->appUserRepository->getApplicationUser();
        $strNotification = 'Event Open';
        if($trnCircleEvents->getTrnCircle()->getMstJoinBy() == 'Open'){
            $notificationService = $this->notificationService;
            foreach ($arrAppUser as $appUser) {
                $notificationService->setAppUser($appUser);
                $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
                $notificationService->setTrnCircleEvents($trnCircleEvents);
                $notificationService->doProcess('Event Open');
            }
        }
    }

    /**
     * @param TrnCircle $trnCircle
     * @param TrnCircleEvents|null $trnCircleEvents
     */
    public function makeEntryForVisitor(TrnCircle $trnCircle, TrnCircleEvents $trnCircleEvents = null)
    {
        if (!empty($this->security->getUser())) {
            $arrParams = array('appUser' => $this->security->getUser(), 'trnCircle' => $trnCircle);
            if (!empty($trnCircleEvents)) {
                $arrParams['trnCircleEvents'] = $trnCircleEvents;
            }
            $trnCircleEventsVisitors = $this->trnCircleEventsVisitorsRepository->findOneBy($arrParams);
            if (empty($trnCircleEventsVisitors)) {
                $objTrnCircleEventsVisitors = new TrnCircleEventsVisitors();
                $objTrnCircleEventsVisitors->setAppUser($this->security->getUser());
                $objTrnCircleEventsVisitors->setTrnCircle($trnCircle);
                if (!empty($trnCircleEvents))
                    $objTrnCircleEventsVisitors->setTrnCircleEvents($trnCircleEvents);
                $objTrnCircleEventsVisitors->setVisitedOn(new \DateTime());
                $objTrnCircleEventsVisitors->setUserIpAddress($_SERVER['REMOTE_ADDR']);
                $this->em->persist($objTrnCircleEventsVisitors);
                $this->em->flush();


                if (!empty($trnCircleEvents)) {
                    //Event Details Page Visitor
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($this->security->getUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->setTrnCircleEvents($trnCircleEvents);
                    $notificationService->doProcess('Event Details Page Visitor');

                    //Event Details Page Creator
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($trnCircleEvents->getAppUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->setRequesterAppUser($this->security->getUser());
                    $notificationService->setTrnCircleEvents($trnCircleEvents);
                    $notificationService->doProcess('Event Details Page Creator');
                } else {
                    //Project Details Page Visitor
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($this->security->getUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Project Details Page Visitor');

                    //Project Details Page Creator
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($trnCircle->getAppUser());
                    $notificationService->setRequesterAppUser($this->security->getUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Project Details Page Creator');
                }
            }
        }
    }

    /**
     * @param TrnCircle $trnCircle
     * @param bool $yearAsKey
     * @param bool $withLimit
     * @param int $limitVal
     * @return array
     */
    public function getGoodnessTimeLineDetails(TrnCircle $trnCircle, $yearAsKey = false, $withLimit = false, $limitVal = 0): array
    {
        if($withLimit == true) {
            $goodnessDetails = $this->em->getRepository(TrnCircleEvents::class)->findBy(
                ['trnCircle' => $trnCircle, 'mstStatus' => 5, 'isActive' => 1, 'isCrowdFunding' => 0 ], ['createdOn' => 'DESC'], $limitVal);
        } else {
            $goodnessDetails = $this->em->getRepository(TrnCircleEvents::class)->findBy(
                ['trnCircle' => $trnCircle, 'mstStatus' => 5, 'isActive' => 1, 'isCrowdFunding' => 0 ], ['createdOn' => 'DESC']);
        }

        $goodnessList = array();
        $goodnessArray = array();
        $yearArray = array();//'1980', '2024','1994'

        foreach($goodnessDetails as $goodnessListtmp) {
            $eventId = $goodnessListtmp->getId();

            $goodnessArray['name'] = $goodnessListtmp->getName();
            $goodnessArray['mstCity'] = $goodnessListtmp->getMstCity()->getCity();
            $goodnessArray['mstState'] = $goodnessListtmp->getMstState()->getState();

            if(!empty($goodnessListtmp->getToDate()) && $goodnessListtmp->getToDate() != '') {
                $eventYear = date("Y", date_timestamp_get($goodnessListtmp->getToDate()));
                if($eventYear <= 0) {
                    $eventYear = date("Y", date_timestamp_get($goodnessListtmp->getCreatedOn()));
                }
            } else {
                $eventYear = date("Y", date_timestamp_get($goodnessListtmp->getCreatedOn()));
            }

            if(!in_array($eventYear,$yearArray)) {
                $yearArray[] = $eventYear;
            }

            $goodnessArray['eventDate'] = $goodnessListtmp->getToDate();
            $goodnessArray['eventHrs'] = 0;
            $goodnessArray['volunteerCnt'] = 0;
            $goodnessArray['materialPer'] = 0;
            $goodnessArray['moneyRaised'] = 0;

            $mediaArr = array();
            $counter = 0;
            $mediaList = $goodnessListtmp->getTrnProductMedia();
            foreach ($mediaList as $mediadetail) {
                if($mediadetail->getMediaType() == 'image'){
                    $mediaArr[] = $mediadetail;
                    $counter++;
                }
                if($counter > 1) {
                    // stop the loop if 2 media files received
                    break;
                }
            }
            $goodnessArray['mediaArr'] = $mediaArr;

            if(count($goodnessListtmp->getTrnVolunterCircleEventDetails()) > 0) {
                $fromTime = $goodnessListtmp->getTrnVolunterCircleEventDetails()[0]->getFromTime();
                $toTime = $goodnessListtmp->getTrnVolunterCircleEventDetails()[0]->getToTime();

                if($fromTime != null && $toTime != null) {
                    $timeformatFrom = date('H:i:s', date_timestamp_get($fromTime));
                    $timeformatTo = date('H:i:s', date_timestamp_get($toTime));

                    $date1 = strtotime($timeformatFrom);
                    $date2 = strtotime($timeformatTo);
                    $interval = $date2 - $date1;
//                    $seconds = $interval % 60;
//                    $minutes = floor(($interval % 3600) / 60);
                    $hours = floor($interval / 3600);
                    $goodnessArray['eventHrs'] = $hours;
                }
            }

            $goodnessArray['volunteerCnt'] = count($goodnessListtmp->getTrnCircleEventRequestToParticipates());

            // Material Percentage
            $totalQtyArr = array();
            $totalMaterialReceived = array();
            if(count($goodnessListtmp->getTrnMaterialInKindCircleEventDetails()) > 0) {

                foreach ($goodnessListtmp->getTrnMaterialInKindCircleEventDetails()[0]->getTrnMaterialInKindCircleEventSubEvents() as $materialInKindCircleEventDetail) {

                    $totalQtyArr[$materialInKindCircleEventDetail->getItemName()] = $materialInKindCircleEventDetail->getItemQuantity();
                    $totalMaterialReceived[$materialInKindCircleEventDetail->getItemName()] = 0; // intialize recieved value
                }

                if(count($goodnessListtmp->getTrnMaterialReceivedAtCollectionCentres()) > 0) {
                    foreach ($goodnessListtmp->getTrnMaterialReceivedAtCollectionCentres() as $materialReceivedAtCollectionCentre) {

                        $totalMaterialReceived[$materialReceivedAtCollectionCentre->getItemName()]+=$materialReceivedAtCollectionCentre->getItemQuantity();
                        /*
                        // TODO: once QR code is in place, apply this condition
                        if($materialReceivedAtCollectionCentre->getIsReceived() == true) {
                            $totalMaterialReceived[$materialReceivedAtCollectionCentre->getItemName()]+=$materialReceivedAtCollectionCentre->getItemQuantity();
                        }
                        */
                    }
                }
            }

            $percentArray = array();
            $percentValue = 0;
            foreach ($totalQtyArr as $itemName=>$totalQty) {
                $currentPercentValue = (100 * $totalMaterialReceived[$itemName]) / $totalQty;
                $percentArray[$itemName] = $currentPercentValue;
                $percentValue+= $currentPercentValue;
            }
            if(count($percentArray) > 0) {
                $goodnessArray['materialPer'] = round( ($percentValue / count($percentArray)), 2);
            }

            if(count($goodnessListtmp->getTrnFundRaiserCircleEventDetails()) > 0) {
                // no time getting entered in fundraiser type events

                $orderRepository = $this->em->getRepository(TrnOrder::class);
                $sql = $orderRepository->createQueryBuilder('o')
                    ->select('sum(o.transactionAmount) as totalTransactionAmount')
                    ->innerJoin('o.trnCircleEvent', 'e')
                    ->andWhere('e.id = :event_id')
                    ->andWhere('e.isActive = 1')
                    ->andWhere('o.transactionStatus = :transactionStatus')
                    ->setParameter('event_id', $eventId)
                    ->setParameter('transactionStatus', 'success')
                    ->getQuery();

                $query = $sql->getResult();
                $totalAmt = 0;
                if (!empty($query) && !empty($query[0]) && !empty($query[0]['totalTransactionAmount'])) {
                    $totalAmt = $query[0]['totalTransactionAmount'];
                }
                $lacConvertor = 100000;
                $goodnessArray['moneyRaised'] = $totalAmt / $lacConvertor;

            }
            if($yearAsKey == true) {
                $goodnessList[$eventYear][] = $goodnessArray;
            } else {
                $goodnessList[] = $goodnessArray;
            }

        }
        rsort($yearArray);

        return array('yearArray' => $yearArray, 'goodnessDetails' => $goodnessList);
    }
}