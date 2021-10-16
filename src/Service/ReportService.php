<?php

namespace App\Service;

use App\Entity\Master\MstEventProductType;
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

class ReportService
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    private $myAccountService;

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
     * @param MyAccountService $myAccountService
     * @param AppUserRepository $appUserRepository
     * @param Security $security
     * @param TrnCircleEventsVisitorsRepository $trnCircleEventsVisitorsRepository
     */
    public function __construct(EntityManagerInterface $em, MyAccountService $myAccountService,
                                AppUserRepository $appUserRepository, Security $security,
                                TrnCircleEventsVisitorsRepository $trnCircleEventsVisitorsRepository) {
        $this->em = $em;
        $this->myAccountService = $myAccountService;
        $this->appUserRepository = $appUserRepository;
        $this->security = $security;
        $this->trnCircleEventsVisitorsRepository = $trnCircleEventsVisitorsRepository;
    }

    public function prepareFundDetailResult($details) {

        $resultArr = array();
        $totalDonationReceivedArr = array();

        $cnt = 1;
        $msr_no = 1;
        foreach ($details as $detail) {
            $tempArr = array();

            $eventObj = $detail->getTrnCircleEvent();
            $circleObj = $eventObj->getTrnCircle();
            $volunteerObj = $eventObj->getAppUser();

            $resultArrKey = $eventObj->getId();

            $tempArr['circleId'] = $circleObj->getId();
            $tempArr['eventId'] = $eventObj->getId();
            $tempArr['event'] = $eventObj;
            $tempArr['isCFEvent'] = $eventObj->getIsCrowdFunding();

            if(!array_key_exists($resultArrKey, $resultArr)) {

                $cnt = 1;
                $tempArr['msr_no'] = $msr_no;
                $msr_no++;
                $tempArr['circleName'] = $circleObj->getCircle();
                $tempArr['eventName'] = $eventObj->getName();
                $tempArr['start_date'] = $eventObj->getFromDate();
                $tempArr['end_date'] = $eventObj->getToDate();

                $resultArr[$resultArrKey][$detail->getId()] = $tempArr;
                $totalDonationReceivedArr[$resultArrKey] = 0;

            } else {
                $tempArr['msr_no'] = '';
                $tempArr['circleName'] = '';
                $tempArr['eventName'] = '';
                $tempArr['start_date'] = '';
                $tempArr['end_date'] = '';

                $resultArr[$resultArrKey][$detail->getId()] = $tempArr;
            }

            $totalDonationReceivedArr[$resultArrKey]+=$detail->getTransactionAmount();

            $resultArr[$resultArrKey][$detail->getId()]['sr_no'] = $cnt;
            $resultArr[$resultArrKey][$detail->getId()]['volunteerName'] = $volunteerObj->getAppUserInfo()->getName();
            $resultArr[$resultArrKey][$detail->getId()]['donorName'] = $detail->getUserFirstName().' '.$detail->getUserLastName();
            $resultArr[$resultArrKey][$detail->getId()]['donationAmount'] = $detail->getTransactionAmount();
            $resultArr[$resultArrKey][$detail->getId()]['isDonorAnonymous'] = $detail->getIsAnonymousDonation() ? 'Yes' : 'No';

            $cnt++;

            /*uasort($resultArr[$resultArrKey], function($a, $b) {
                $retval = $a['msr_no'] <=> $b['msr_no'];
                if ($retval == 0) {
                    $retval = $a['sr_no'] <=> $b['sr_no'];
                    if ($retval == 0) {
                        $retval = $a['sr_no'] <=> $b['sr_no'];
                    }
                }
                return $retval;
            });*/

        }

        $returnArr['resultDetail'] = $resultArr;
        $returnArr['donationDetail'] = $totalDonationReceivedArr;

        return $returnArr;
    }

    public function prepareImpactReportResult($details) {

        $returnArr = array();
        $resultArr = array();
        $eventDetailArr = array();
        $volunteerDetailArr = array();
        $materialDetailArr = array();
        $fundsDetailArr = array();
        $volunteerTotalHoursAchieved = array();
        $fundTotalFundsAchieved = array();

        $entityManager = $this->em;
        $emptyEventDetail = array('esr_no'=> '', 'circleName' => '', 'eventName' => '', 'resourceRequired' => '');
        $emptyMaterialDetail = array('msr_no' => '', 'material_start_date' => '', 'material_end_date' => '', 'item' => '',
            'qty_required' => '', 'm_participant_name' => '', 'qty_received' => '', 'collection_center' => '');
        $emptyVolunteerDetail = array('vsr_no' => '', 'volunteer_start_date' => '', 'volunteer_end_date' => '', 'place_of_work'=> '',
            'total_hours_asked' => '', 'total_hours_achieved' => '', 'skill' => '', 'hours_asked' => '', 'v_participant_name' => '',
            'date_of_service' => '', 'hours_achieved' => '');
        $emptyFundDetail = array('fsr_no'=>'', 'fund_start_date' => '', 'fund_end_date' => '', 'total_funds_required' => '', 'total_funds_received' => '',
            'date_of_donation' => '', 'donor_name' => '', 'donation_amount' => '');

        $eCnt = 1;
        foreach ($details as $detail) {
            $tempArr = array();

            /*$arrEventUpComingOrOnGoingDetails =  $trnCircleEventsRepository->getEventUpComingOrOnGoingDetails
            ([$detail], $entityManager);*/

            $event_id = $detail->getId();
            $resultArrKey = $event_id;
            $totalHoursAchieved = 0;
            $totalfundsAchieved = 0;

            $resourcesArr = array();

            $tempArr['esr_no'] = $eCnt;
            $tempArr['circleName'] = $detail->getTrnCircle();
            $tempArr['eventName'] = $detail->getName();
            $tempArr['resourceRequired'] = implode(', ', $resourcesArr);
            $eCnt++;

            $eventDetailArr[$resultArrKey] = $tempArr;

            foreach ($detail->getMstEventProductType() as $productType) {

                $resource = $productType->getEventProductType();
                $resourcesArr[] = $resource;

                // Volunteer Details - start
                if($resource == 'Volunteer (in Time)') {

                    $volunteerArr = $detail->getTrnVolunterCircleEventDetails();

                    if(!empty($volunteerArr) && !empty($volunteerArr[0])){
                        $trnVolunteerCircleEventDetails = $volunteerArr[0];
                        $vStartDate = $trnVolunteerCircleEventDetails->getFromDate();
                        $vEndDate = $trnVolunteerCircleEventDetails->getToDate();
                        $placeOfWork = $trnVolunteerCircleEventDetails->getMstPlaceOfWork()->getPlaceOfWork();
                        $totalHoursAsked = 0;
//dd($trnVolunteerCircleEventDetails);
                        $vItemArr = array();
                        foreach ($trnVolunteerCircleEventDetails->getTrnVolunterCircleEventSubEvents() as $trnVolunterCircleEventSubEvents) {
                            $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['specific_skill'] = $trnVolunterCircleEventSubEvents->getSubEventName();
                            $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['hours_asked'] = $trnVolunterCircleEventSubEvents->getNumberOfHours();
                            $totalHoursAsked+= $trnVolunterCircleEventSubEvents->getNumberOfHours();
                        }
                        $vCnt = 1;
                        $tempArrVolunteer = array();
                        $tempArrVolunteer['vsr_no'] = $vCnt;
                        if ($vCnt == 1) {
                            $tempArrVolunteer['volunteer_start_date'] = $vStartDate;
                            $tempArrVolunteer['volunteer_end_date'] = $vEndDate;
                            $tempArrVolunteer['place_of_work'] = $placeOfWork;
                            $tempArrVolunteer['total_hours_asked'] = $totalHoursAsked;
                        } else {
                            $tempArrVolunteer['volunteer_start_date'] = '';
                            $tempArrVolunteer['volunteer_end_date'] = '';
                            $tempArrVolunteer['place_of_work'] = '';
                            $tempArrVolunteer['total_hours_asked'] = '';
                        }

                        foreach ($trnVolunteerCircleEventDetails->getTrnVolunteerCircleParticipationDetails() as $vDetail) {

                            if ($vCnt > 1) {
                                $tempArrVolunteer = array();
                                $tempArrVolunteer['vsr_no'] = $vCnt;
                                $tempArrVolunteer['volunteer_start_date'] = '';
                                $tempArrVolunteer['volunteer_end_date'] = '';
                                $tempArrVolunteer['place_of_work'] = '';
                                $tempArrVolunteer['total_hours_asked'] = '';
                            }

                            $tempArrVolunteer['total_hours_achieved'] = '';
                            $tempArrVolunteer['skill'] = $vItemArr[$vDetail->getTrnVolunterCircleEventSubEvent()->getId()]['specific_skill'];
                            $tempArrVolunteer['hours_asked'] = $vItemArr[$vDetail->getTrnVolunterCircleEventSubEvent()->getId()]['hours_asked'];
                            $tempArrVolunteer['v_participant_name'] = $vDetail->getAppUser()->getAppUserInfo()->getName();
                            $tempArrVolunteer['date_of_service'] = $vDetail->getDateOfService();
                            $tempArrVolunteer['hours_achieved'] = $vDetail->getNumberOfHours();
                            $totalHoursAchieved+=$vDetail->getNumberOfHours();
                            $vCnt++;

                            $volunteerDetailArr[$resultArrKey][] = $tempArrVolunteer;
                            $volunteerTotalHoursAchieved[$resultArrKey] = $totalHoursAchieved;
                        }

                        if(count($trnVolunteerCircleEventDetails->getTrnVolunteerCircleParticipationDetails()) == 0) {
                            $tempArrVolunteer['vsr_no'] = '';
                            $tempArrVolunteer['skill'] = '';
                            $tempArrVolunteer['hours_asked'] = '';
                            $tempArrVolunteer['v_participant_name'] = '';
                            $tempArrVolunteer['date_of_service'] = '';
                            $tempArrVolunteer['hours_achieved'] = '';


                            $volunteerDetailArr[$resultArrKey][] = $tempArrVolunteer;
                            $volunteerTotalHoursAchieved[$resultArrKey] = 0;

                        }
                    }

                }
                // Volunteer Details - end

                // Material Details - start
                if($resource == 'Material (in Kind)') {

                    $materialArr = $detail->getTrnMaterialInKindCircleEventDetails();
                    if(!empty($materialArr) && !empty($materialArr[0])){
                        $trnMaterialInKindCircleEventDetails = $materialArr[0];
                        $mStartDate = $trnMaterialInKindCircleEventDetails->getFromDate();
                        $mEndDate = $trnMaterialInKindCircleEventDetails->getToDate();

                        $itemArr = array();
                        foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventSubEvents() as $trnMaterialInKindCircleEventSubEvent) {
                            $itemArr[$trnMaterialInKindCircleEventSubEvent->getItemName()] =
                                $trnMaterialInKindCircleEventSubEvent->getItemQuantity().' '.$trnMaterialInKindCircleEventSubEvent->getUnit();
                        }

                        $mCnt = 1;
                        $tempArrMaterial = array();
                        $tempArrMaterial['msr_no'] = $mCnt;

                        if($mCnt == 1) {
                            $tempArrMaterial['material_start_date'] = $mStartDate;
                            $tempArrMaterial['material_end_date'] = $mEndDate;
                        } else {
                            $tempArrMaterial['material_start_date'] = '';
                            $tempArrMaterial['material_end_date'] = '';
                        }
                        foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialReceivedAtCollectionCentres() as $mDetail) {

                            $cCObj = $mDetail->getTrnCollectionCentreDetails();
                            $collectionCenter = $cCObj->getFirstName().' '.$cCObj->getLastName().
                                '<br>'.$cCObj->getAddress1().', '.$cCObj->getAddress2().', '.
                                $cCObj->getMstCity().', '.$cCObj->getMstState().', '.$cCObj->getMstCountry().', '.$cCObj->getPinCode();

                            if($mCnt > 1) {
                                $tempArrMaterial = array();
                                $tempArrMaterial['msr_no'] = $mCnt;
                                $tempArrMaterial['material_start_date'] = '';
                                $tempArrMaterial['material_end_date'] = '';
                            }

                            $tempArrMaterial['item'] = $mDetail->getItemName();
                            $tempArrMaterial['qty_required'] = $itemArr[$mDetail->getItemName()];
                            $tempArrMaterial['m_participant_name'] = $mDetail->getAppUser()->getAppUserInfo()->getName();
                            $tempArrMaterial['qty_received'] = $mDetail->getItemQuantity().' '.$mDetail->getItemUnit();
                            $tempArrMaterial['collection_center'] = $collectionCenter;
                            $mCnt++;

                            $materialDetailArr[$resultArrKey][] = $tempArrMaterial;
                        }

                        if(count($trnMaterialInKindCircleEventDetails->getTrnMaterialReceivedAtCollectionCentres()) == 0) {

                            $tempArrMaterial['msr_no'] = '';
                            $tempArrMaterial['item'] = '';
                            $tempArrMaterial['qty_required'] = '';
                            $tempArrMaterial['m_participant_name'] = '';
                            $tempArrMaterial['qty_received'] = '';
                            $tempArrMaterial['collection_center'] = '';

                            $materialDetailArr[$resultArrKey][] = $tempArrMaterial;
                        }

                    }

                }
                // Material Details - end

                // Fundraiser Details - start
                if($resource == 'Fundraiser') {

                    $FundraiserArr = $detail->getTrnFundRaiserCircleEventDetails();
                    if(!empty($FundraiserArr) && !empty($FundraiserArr[0])){
                        $trnFundCircleEventDetails = $FundraiserArr[0];

                        $fStartDate = $trnFundCircleEventDetails->getFromDate();
                        $fEndDate = $trnFundCircleEventDetails->getToDate();
                        $totalFundsRequired = $trnFundCircleEventDetails->getTargetAmount();

                        $fCnt = 1;
                        $tempArrFund = array();

                        $tempArrFund['fsr_no'] = $fCnt;

                        if ($fCnt == 1) {
                            $tempArrFund['fund_start_date'] = $fStartDate;
                            $tempArrFund['fund_end_date'] = $fEndDate;
                            $tempArrFund['total_funds_required'] = $totalFundsRequired;
                        } else {
                            $tempArrFund['fund_start_date'] = '';
                            $tempArrFund['fund_end_date'] = '';
                            $tempArrFund['total_funds_required'] = '';
                        }

                        foreach ($detail->getTrnOrders() as $fDetail) {

                            if($fDetail->getTransactionStatus() == 'success') {

                                if ($fCnt > 1) {

                                    $tempArrFund = array();
                                    $tempArrFund['fsr_no'] = $fCnt;
                                    $tempArrFund['fund_start_date'] = '';
                                    $tempArrFund['fund_end_date'] = '';
                                    $tempArrFund['total_funds_required'] = '';
                                }

                                $tempArrFund['total_funds_received'] = '';
                                $tempArrFund['date_of_donation'] = $fDetail->getOrderDateTime();
                                $tempArrFund['donor_name'] = $fDetail->getUserFirstName() . ' ' . $fDetail->getUserLastName();
                                $tempArrFund['donation_amount'] = $fDetail->getTransactionAmount();

                                $totalfundsAchieved += $fDetail->getTransactionAmount();
                                $fCnt++;

                                $fundsDetailArr[$resultArrKey][] = $tempArrFund;
                                $fundTotalFundsAchieved[$resultArrKey] = $totalfundsAchieved;
                            }
                        }
                        if(count($detail->getTrnOrders()) == 0) {
                            $tempArrFund['fsr_no'] = '';
                            $tempArrFund['total_funds_received'] = '';
                            $tempArrFund['date_of_donation'] = '';
                            $tempArrFund['donor_name'] = '';
                            $tempArrFund['donation_amount'] = '';

                            $fundsDetailArr[$resultArrKey][] = $tempArrFund;
                            $fundTotalFundsAchieved[$resultArrKey] = 0;
                        }
                    }

                }
                // Fundraiser Details - end

            }

        }

        foreach ($eventDetailArr as $key=>$detail) {

            $matCnt = array_key_exists($key, $materialDetailArr) ? count($materialDetailArr[$key]) : 0;
            $volCnt = array_key_exists($key, $volunteerDetailArr) ? count($volunteerDetailArr[$key]) : 0;
            $fundCnt = array_key_exists($key, $fundsDetailArr) ? count($fundsDetailArr[$key]) : 0;

            if($matCnt > $volCnt) {
                $maxResultRow = $matCnt;
            } else {
                $maxResultRow = $volCnt;
            }

            if($fundCnt > $maxResultRow) {
                $maxResultRow = $fundCnt;
            }

            // if no resource type entry found, need to add the condition
            if($maxResultRow == 0) {
                $maxResultRow = 1;
            }

            for ($i = 0; $i < $maxResultRow; $i++) {

                if($i == 0) {
                    $mergedResult = $detail;
                } else {
                    $mergedResult = $emptyEventDetail;
                }

                if(array_key_exists($key, $volunteerDetailArr) && array_key_exists($i, $volunteerDetailArr[$key])) {
                    $mergedResult += $volunteerDetailArr[$key][$i];
                } else {
                    $mergedResult += $emptyVolunteerDetail;
                }

                if(array_key_exists($key, $materialDetailArr) && array_key_exists($i, $materialDetailArr[$key])) {
                    $mergedResult += $materialDetailArr[$key][$i];
                } else {
                    $mergedResult += $emptyMaterialDetail;
                }

                if(array_key_exists($key, $fundsDetailArr) && array_key_exists($i, $fundsDetailArr[$key])) {
                    $mergedResult += $fundsDetailArr[$key][$i];
                } else {
                    $mergedResult += $emptyFundDetail;
                }

                $resultArr[$key][$i] = $mergedResult;
            }

        }

        $returnArr['resultArr'] = $resultArr;
        $returnArr['volunteerHrsAchieved'] = $volunteerTotalHoursAchieved;

        return $returnArr;

    }

    public function prepareEventPerformanceResult($details) {

        $resultArr = array();
        $eventDetailArr = array();
        $volunteerDetailArr = array();
        $materialDetailArr = array();
        $fundsDetailArr = array();

        $mainCnt = 1;
        foreach ($details as $detail) {
            $tempArr = array();

            $circle_id = $detail->getTrnCircle()->getId();
            $event_id = $detail->getId();

            //$resultArrKey = $circle_id.'_'.$event_id;
            $resultArrKey = $event_id;

            $countData = $this->myAccountService->getEventMemberList($detail); // get counts of members per resource type

            /*$materialMemberCnt = 0;
            $volunteerMemberCnt = 0;
            $fundMemberCnt = 0;
            */
            $membersJoined = 0;
            $membersPending = 0;
            foreach($countData['accepted'] as $key=>$acceptedCnt) {

                /*if($key == 'Volunteer (in Time)') {
                    $volunteerMemberCnt+=$acceptedCnt;
                }
                if($key == 'Material (in Kind)') {
                    $materialMemberCnt+=$acceptedCnt;
                }
                if($key == 'Fundraiser') {
                    $fundMemberCnt+=$acceptedCnt;
                }*/
                $membersJoined+=$acceptedCnt;
            }
            foreach($countData['pending'] as $key=>$pendingCnt) {
                //if($key == 'Volunteer (in Time)' || $key == 'Material (in Kind)')
                $membersPending += $pendingCnt;
            }

            // Volunteer Participant Details - start
            if(count($detail->getTrnVolunterCircleEventDetails()) > 0) {
                $trnVolunteerCircleEventDetails = $detail->getTrnVolunterCircleEventDetails()[0];

                $vItemArr = array();
                foreach ($trnVolunteerCircleEventDetails->getTrnVolunterCircleEventSubEvents() as $trnVolunterCircleEventSubEvents) {
                    $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['specific_skill'] = $trnVolunterCircleEventSubEvents->getSubEventName();
                    $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['hours_asked'] = $trnVolunterCircleEventSubEvents->getNumberOfHours();
                    $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['v_participant_count'] = 0;
                    $vItemArr[$trnVolunterCircleEventSubEvents->getId()]['hours_achieved'] = 0;
                }

                foreach ($trnVolunteerCircleEventDetails->getTrnVolunteerCircleParticipationDetails() as $vDetail) {

                    $vItemArr[$vDetail->getTrnVolunterCircleEventSubEvent()->getId()]['v_participant_count']++;
                    $vItemArr[$vDetail->getTrnVolunterCircleEventSubEvent()->getId()]['hours_achieved']+= $vDetail->getNumberOfHours();
                }
                $volunteerDetailArr[$resultArrKey] = array_values($vItemArr);
            }
            // Volunteer Participant Details - end

            // Material Participant Details - start
            $totalMemberPerItem = array();
            $totalQtyArr = array();
            $totalMaterialReceived = array();
            if(count($detail->getTrnMaterialInKindCircleEventDetails()) > 0) {

                foreach ($detail->getTrnMaterialInKindCircleEventDetails()[0]->getTrnMaterialInKindCircleEventSubEvents() as $materialInKindCircleEventDetail) {

                    $totalQtyArr[$materialInKindCircleEventDetail->getItemName()] = $materialInKindCircleEventDetail->getItemQuantity();
                    $totalMaterialReceived[$materialInKindCircleEventDetail->getItemName()] = 0; // intialize recieved value
                    $totalMemberPerItem[$materialInKindCircleEventDetail->getItemName()] = 0; // intialize member cnt value
                }

                if(count($detail->getTrnMaterialReceivedAtCollectionCentres()) > 0) {
                    foreach ($detail->getTrnMaterialReceivedAtCollectionCentres() as $materialReceivedAtCollectionCentre) {

                        $totalMaterialReceived[$materialReceivedAtCollectionCentre->getItemName()]+=$materialReceivedAtCollectionCentre->getItemQuantity();
                        $totalMemberPerItem[$materialReceivedAtCollectionCentre->getItemName()]++;
                    }
                }
            }
            foreach ($totalQtyArr as $skillKey=>$qtyValue) {

                $tmpArr = array();
                $tmpArr['memberCnt'] = $totalMemberPerItem[$skillKey];
                $tmpArr['item'] = $skillKey;
                $tmpArr['qtyRequired'] = $qtyValue;
                $tmpArr['qtyReceived'] = $totalMaterialReceived[$skillKey];

                $materialDetailArr[$resultArrKey][] = $tmpArr;
            }
            // Material Participant Details - end

            // Fund Details - start
            $totalFundsRequired = 0;
            $totalFundReceived = 0;
            $no_of_donors = 0;
            if(count($detail->getTrnFundRaiserCircleEventDetails()) > 0) {
                $trnFundCircleEventDetails = $detail->getTrnFundRaiserCircleEventDetails()[0];
                $totalFundsRequired = $trnFundCircleEventDetails->getTargetAmount();

                foreach ($detail->getTrnOrders() as $fDetail) {

                    if($fDetail->getTransactionStatus() == 'success') {
                        $no_of_donors++;
                        $totalFundReceived+= $fDetail->getTransactionAmount();
                    }
                }
            }
            // Fund Details - end

            if(!array_key_exists($resultArrKey, $eventDetailArr)) {

                $tempArr['sr_no'] = $mainCnt;
                $mainCnt++;
                $tempArr['circleName'] = $detail->getTrnCircle()->getCircle();
                $tempArr['circleId'] = $circle_id;
                $tempArr['eventName'] = $detail->getName();
                $tempArr['eventId'] = $event_id;
                $tempArr['no_of_invitation'] = $countData['totalMemberCount'];
                $tempArr['members_joined'] = $membersJoined;
                $tempArr['members_pending'] = $membersPending;
                $tempArr['fund_no_of_participant'] = $no_of_donors;
                $tempArr['fund_total_amt_required'] = $totalFundsRequired;
                $tempArr['fund_total_amt_received'] = $totalFundReceived;

                $eventDetailArr[$resultArrKey] = $tempArr;
            }
        }
//dd($eventDetailArr);

        $emptyEventDetail = [
            'sr_no' => '', 'circleName' => '', 'circleId' => '', 'eventName' => '', 'eventId' => '', 'no_of_invitation' => '',
            'members_joined' => '', 'members_pending' => '', 'fund_no_of_participant' => '', 'fund_total_amt_required' => '',
            'fund_total_amt_received' => ''
        ];
        $emptyVolunteerDetail = [
            'specific_skill' => '', 'hours_asked' => '', 'v_participant_count' => '',  'hours_achieved' => ''
        ];
        $emptyMaterialDetail = [
            'memberCnt' => '', 'item' => '', 'qtyRequired' => '', 'qtyReceived'=> ''
        ];

        foreach ($eventDetailArr as $key=>$detail) {

            $matCnt = array_key_exists($key, $materialDetailArr) ? count($materialDetailArr[$key]) : 0;
            $volCnt = array_key_exists($key, $volunteerDetailArr) ? count($volunteerDetailArr[$key]) : 0;

            if($matCnt > $volCnt) {
                $maxResultRow = $matCnt;
            } else {
                $maxResultRow = $volCnt;
            }

            // if only fund event there, need to add the condition
            if($maxResultRow == 0) {
                $maxResultRow = 1;
            }

            for ($i = 0; $i < $maxResultRow; $i++) {

                if($i == 0) {
                    $mergedResult = $detail;
                } else {
                    $mergedResult = $emptyEventDetail;
                }

                if(array_key_exists($key, $volunteerDetailArr) && array_key_exists($i, $volunteerDetailArr[$key])) {
                    $mergedResult += $volunteerDetailArr[$key][$i];
                } else {
                    $mergedResult += $emptyVolunteerDetail;
                }

                if(array_key_exists($key, $materialDetailArr) && array_key_exists($i, $materialDetailArr[$key])) {
                    $mergedResult += $materialDetailArr[$key][$i];
                } else {
                    $mergedResult += $emptyMaterialDetail;
                }
//dd($mergedResult);
                $resultArr[$key][$i] = $mergedResult;
            }

        }

        return $resultArr;
    }
}