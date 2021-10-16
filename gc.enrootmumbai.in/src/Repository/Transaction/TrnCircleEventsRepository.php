<?php

namespace App\Repository\Transaction;

use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Master\MstStatusRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
//use Doctrine\ORM\QueryBuilder;
use \DateTime;

/**
 * @method TrnCircleEvents|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrnCircleEvents|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrnCircleEvents[]    findAll()
 * @method TrnCircleEvents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrnCircleEventsRepository extends ServiceEntityRepository
{
    /**
     * @var TrnCircleEventLeadsRepository
     */
    private $trnCircleEventLeadsRepository;

    /**
     * @var MstStatusRepository
     */
    private $mstStatusRepository;

    public function __construct(ManagerRegistry $registry, TrnCircleEventLeadsRepository
    $trnCircleEventLeadsRepository, MstStatusRepository $mstStatusRepository)
    {
        parent::__construct($registry, TrnCircleEvents::class);
        $this->trnCircleEventLeadsRepository = $trnCircleEventLeadsRepository;
        $this->mstStatusRepository = $mstStatusRepository;
    }

    public function getPrimaryAreaInterestList($company_id, $activeEventsOnly = false)
    {
        $sql = $this->createQueryBuilder('a')
            ->select('tap.id', 'tap.areaInterest')
            ->leftJoin('a.trnAreaOfInterests', 'ta')
            ->leftJoin('ta.areaInterestPrimary', 'tap')
            ->andWhere('a.orgCompany =:company')
            ->andWhere('tap.isActive =:active')
            ->setParameter('company', $company_id)
            ->setParameter('active', 1)
            ->groupBy('tap.id')
            ->orderBy('tap.areaInterest', 'ASC');

        if($activeEventsOnly == true) {
            $sql->leftJoin('a.mstStatus', 'mst')
                ->andWhere('mst.status =:masterStatus')
                ->setParameter('masterStatus', 'Activated');
        }

        $query = $sql->getQuery();

        /*echo '<pre>';
        print_r($query->getParameters());
        echo '</pre>';
        dd($query->getSQL());*/

        return $query->getResult();
    }

    public function findByTextSearch($arrSearch) {

        $q = $this->createQueryBuilder('e');

        foreach ($arrSearch as $searchKey => $searchValue) {

            if($searchKey == 'textSearch') {

                $strCondition = "mc.city like :search_text 
                    OR ms.state like :search_text 
                    OR mt.country like :search_text
                    OR map.areaInterest like :search_text
                    OR mas.areaInterest like :search_text";
                $q->leftJoin('e.mstCity', 'mc')
                    ->leftJoin('e.mstState', 'ms')
                    ->leftJoin('e.mstCountry', 'mt')
                    ->leftJoin('e.trnAreaOfInterests', 'ta')
                    ->leftJoin('ta.areaInterestPrimary', 'map')
                    ->leftJoin('ta.areaInterestSecondary', 'mas')
                    ->andWhere($strCondition)
                    ->setParameter('search_text', "%".$searchValue."%");

            } else {
                $q->andWhere('e.'.$searchKey.' = :'.$searchKey)
                    ->setParameter($searchKey, $searchValue);
            }
        }
/*
        if (!empty($searchText)) {
            $strCondition = " c.name like :search_text OR mc.city like :search_text OR ms.state like :search_text OR mt.country like :search_text";
            $q->leftJoin('c.mstCity', 'mc')
                ->leftJoin('c.mstState', 'ms')
                ->leftJoin('c.mstCountry', 'mt')
                ->andWhere($strCondition)
                ->setParameter('search_text', "%".$searchText."%");
        }
        $q->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setParameter('status', $objMstStatus->getId())
            ->setParameter('circle_id', $nCircleId)
            ->setMaxResults( 3 )
            ->orderBy('c.createdOn','DESC');*/
        $query = $q->getQuery();

        return $query->getResult();
    }

    public function getCircleEventList($circle_id)
    {
        return $this->createQueryBuilder('e')
            ->select('e.id', 'e.name')
            ->andWhere('e.trnCircle =:circle')
            ->setParameter('circle', $circle_id)
            ->orderBy('e.name', 'ASC')
            ->getQuery()
            ->getResult();

    }

    /**
     * @param $nCircleId
     * @param $eventTime
     * @param $company_id
     * @param $arrMstEventProductType
     * @param MstStatus $objMstStatus
     * @param string $searchText
     * @param MstStatus $expiredObjMstStatus
     * @return mixed
     */
    public function getAllEventOnFilter($nCircleId, $eventTime, $company_id, $arrMstEventProductType,MstStatus
    $objMstStatus, $searchText = "",MstStatus $expiredObjMstStatus){
        $strCurrentDate = date('Y-m-d');
        $q = $this->createQueryBuilder('c');
        $q->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.mstStatus = :status OR c.mstStatus = :exp_status')
            ->andWhere('c.trnCircle = :circle_id');
        if(!empty($arrMstEventProductType)) {
            $q->innerJoin('c.mstEventProductType', 'p')
                ->andWhere('p.id in (:mstEventProductType_id)')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }
        if (!empty($eventTime)) {
            if(count($eventTime) < 3) {
                $arrCondition = array();
                foreach ($eventTime as $strEventTime) {
                    switch (strtolower($strEventTime)) {
                        case 'upcoming':{
                            $arrCondition[] = " c.fromDate > :current_date";
                            break;
                        }
                        case 'ongoing':{
                            $arrCondition[] = " (c.fromDate <= :current_date and c.toDate >= :current_date )";
                            break;
                        }
                        case 'past':{
                            $arrCondition[] = " c.toDate < :current_date OR c.isTargetAchieved = 1";
                            break;
                        }
                    }
                }
                $strFinalQuery = implode(' OR ', $arrCondition);
                $q->andWhere($strFinalQuery)
                    ->setParameter('current_date', $strCurrentDate);
            }
        }
        if (!empty($searchText)) {
            $strCondition = " c.name like :search_text OR mc.city like :search_text OR ms.state like :search_text OR mt.country like :search_text";
            $q->leftJoin('c.mstCity', 'mc')
                ->leftJoin('c.mstState', 'ms')
                ->leftJoin('c.mstCountry', 'mt')
                ->andWhere($strCondition)
                ->setParameter('search_text', "%".$searchText."%");
        }
        $q->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setParameter('status', $objMstStatus->getId())
            ->setParameter('exp_status', $expiredObjMstStatus->getId())
            ->setParameter('circle_id', $nCircleId)
            ->setMaxResults( 3 )
            ->orderBy('c.createdOn','DESC');
        $query = $q->getQuery();
        return $query->getResult();
    }

    /**
     * @param MstStatus $objMstStatus
     * @param $company_id
     * @return mixed
     */
    public function getLatestEvents(MstStatus $objMstStatus, $company_id) {
        $strCurrentDate = date('Y-m-d');
        $q = $this->createQueryBuilder('c');
        $q->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.mstStatus = :status');
        if(!empty($arrMstEventProductType)) {
            $q->innerJoin('c.mstEventProductType', 'p')
                ->andWhere('p.id in (:mstEventProductType_id)')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }
        if (!empty($eventTime)) {
            if(count($eventTime) < 3) {
                $arrCondition = array();
                foreach ($eventTime as $strEventTime) {
                    switch (strtolower($strEventTime)) {
                        case 'upcoming':{
                            $arrCondition[] = " c.fromDate > :current_date";
                            break;
                        }
                        case 'ongoing':{
                            $arrCondition[] = " (c.fromDate <= :current_date and c.toDate >= :current_date )";
                            break;
                        }
                        case 'past':{
                            $arrCondition[] = " c.toDate < :current_date OR c.isTargetAchieved = 1";
                            break;
                        }
                    }
                }
                $strFinalQuery = implode(' OR ', $arrCondition);
                $q->andWhere($strFinalQuery)
                    ->setParameter('current_date', $strCurrentDate);
            }
        }
        $q->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setParameter('status', $objMstStatus->getId())
            ->setMaxResults( 4 )
            ->orderBy('c.createdOn','DESC');
        $query = $q->getQuery();
        return $query->getResult();
    }

    /**
     * @param mixed $objMstStatus
     * @param $company_id
     * @param AppUser|null $appUser
     * @param array $arrMstEventProductType
     * @param array $arrParameters
     * @return mixed
     */
    public function getAllEventsOfUser($objMstStatus, $company_id, AppUser $appUser = null, $arrMstEventProductType = array(), $arrParameters = array()) {
        $strCurrentDate = date('Y-m-d');
        $arrStatus = array();

        if (is_array($objMstStatus)) {
            foreach ($objMstStatus as $status){
                $arrStatus[] = $status->getId();
            }
        } else {
            $arrStatus[] = $objMstStatus->getId();
        }
        //$strStatus = implode(',',$arrStatus);
        $q = $this->createQueryBuilder('c');
        $q->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company')
            ->andWhere('c.appUser = :appUser');
        if (!empty($arrStatus)){
            $q->andWhere('c.mstStatus in (:status) ')
                ->setParameter('status', $arrStatus);
        }
        if(!empty($arrMstEventProductType)) {

            if (!empty($arrParameters) && !empty($arrParameters['copySearch'])) {

                if($arrParameters['copySearch']['includeCrowdfunding'] == 'no') {
                    $q->andWhere('c.isCrowdFunding = :crowdfunding')
                        ->setParameter('crowdfunding' , 0);
                }

                $strCondition = '';

                if (!empty($arrParameters['copySearch']['searchText'])) {
                    $strCondition .= "c.name like :copysearchText";
                }
                if (!empty($arrParameters['copySearch']['status'])) {

                    if ($arrParameters['copySearch']['status'] == 'upcoming') {
                        $strCondition .= " OR c.fromDate > :currDate";
                    } else if ($arrParameters['copySearch']['status'] == 'ongoing') {
                        $strCondition .= " OR (c.fromDate <= :currDate and c.toDate >= :currDate)";
                    } else {
                        $strCondition .= " OR (c.toDate < :currDate OR c.isTargetAchieved = 1)";
                    }
                }
                if (!empty($strCondition)) {

                    if(count($arrMstEventProductType) >= 3) {

                        // no need to join event product type as all product type is need to be seen
                        $q->innerJoin('c.mstEventProductType', 'p')
                            ->andWhere('('.$strCondition.')')
                            ->setParameter('copysearchText', "%" . $arrParameters['copySearch']['searchText'] . "%");
                    } else {
                        $q->innerJoin('c.mstEventProductType', 'p')
                            ->andWhere('p.id in (:mstEventProductType_id) OR '.$strCondition)
                            ->setParameter('mstEventProductType_id', $arrMstEventProductType)
                            ->setParameter('copysearchText', "%" . $arrParameters['copySearch']['searchText'] . "%");
                    }
                }
                if (!empty($arrParameters['copySearch']['status'])) {
                    $q->setParameter('currDate', $strCurrentDate . ' 00:00:00');
                }
            } else {
                $q->innerJoin('c.mstEventProductType', 'p')
                    ->andWhere('p.id in (:mstEventProductType_id)')
                    ->setParameter('mstEventProductType_id', $arrMstEventProductType);
            }
        }
        if (!empty($arrParameters)) {
            if (!empty($arrParameters['quicksearch'])) {
                $q->innerJoin('c.trnCircle', 'tc')
                  ->andWhere('c.name like :quicksearch OR tc.circle like :quicksearch')
                  ->setParameter('quicksearch', "%$arrParameters[quicksearch]%");
            }
            if (!empty($arrParameters['from']) && !empty($arrParameters['to']) ) {
                $q->andWhere('(c.fromDate >= :fromdate AND c.fromDate <= :todate ) OR (c.toDate >= :fromdate AND c.toDate <= :todate )')
                  ->setParameter('fromdate', $arrParameters['from'])
                  ->setParameter('todate', $arrParameters['to']);
            }
        }
        $q->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setParameter('appUser', $appUser->getId())
            ->orderBy('c.createdOn','DESC');

        /*$query = $q->getQuery() ;
        echo '<pre>';
        print_r($query->getParameters());
        echo '</pre>';
        dd($query->getSQL());*/

        $arrOwnEvents = $q->getQuery()->getResult();

        //dd($arrOwnEvents);

        if (!empty($arrParameters) && !empty($arrParameters['copySearch'])) {
            // no co-core events for copy event list
        } else {
            if (!empty($appUser)){
                $arrCoCoreEvents = $this->getCoCoreEvents($objMstStatus, $company_id, $appUser, $arrMstEventProductType, $arrParameters);
                $arrOwnEvents = array_merge($arrOwnEvents, $arrCoCoreEvents);
            }
        }
        return $arrOwnEvents;
    }


    /**
     * @param $objMstStatus
     * @param $company_id
     * @param AppUser|null $appUser
     * @param array $arrMstEventProductType
     * @param array $arrParameters
     * @return array
     */
    private function getCoCoreEvents($objMstStatus, $company_id, AppUser $appUser = null, $arrMstEventProductType =
    array(), array $arrParameters = array())
    {
        if (is_array($objMstStatus)) {
            foreach ($objMstStatus as $status) {
                $arrStatus[] = $status->getId();
            }
        } else {
            $arrStatus[] = $objMstStatus->getId();
        }
        $objMstStatusActivated = $this->mstStatusRepository->findOneBy(["status" => 'Activated']);
        $myCoCorePrjsEvents = $this->trnCircleEventLeadsRepository->createQueryBuilder('c')
            ->addSelect('tc')
            ->innerJoin('c.trnCircle', 'tc')
            ->innerJoin('tc.trnCircleEvents', 'tce')
            ->where("c.isActive = :isActive")
            ->andWhere("tc.isActive = :isActive_id")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("tc.mstStatus = :mstStatus");
        if (!empty($strStatus)) {
            $myCoCorePrjsEvents->andWhere('tce.mstStatus in (:status) ')
                ->setParameter('status', $strStatus);
        }
        if (!empty($arrMstEventProductType)) {
            $myCoCorePrjsEvents->innerJoin('tce.mstEventProductType', 'p')
                ->andWhere('p.id in (:mstEventProductType_id)')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }
        if (!empty($arrParameters)) {
            if (!empty($arrParamters['quicksearch'])) {
                $myCoCorePrjsEvents->andWhere('tce.name like :quicksearch OR tc.circle like :quicksearch')
                    ->setParameter('status', "%$arrParameters[quicksearch]%");
            }
            if (!empty($arrParameters['from']) && !empty($arrParameters['to']) ) {
                $myCoCorePrjsEvents->andWhere('(tce.fromDate >= :fromdate AND tce.fromDate <= :todate ) OR (tce.toDate >= :fromdate AND tce.toDate <= :todate )')
                    ->setParameter('fromdate', $arrParameters['from'])
                    ->setParameter('todate', $arrParameters['to']);
            }
        }
        $myCoCorePrjsEvents->setParameter('isActive', 1)
            ->setParameter('appUser', $appUser)
            ->setParameter('mstStatus', $objMstStatusActivated)
            ->setParameter('isActive_id', 1);
        $myCoCorePrjsEventData = $myCoCorePrjsEvents->getQuery()->getResult();
        $query = array();
        foreach ($myCoCorePrjsEventData as $prj) {
            foreach ($prj->getTrnCircle()->getTrnCircleEvents() as $trnCircleEvent) {
                $query[] = $trnCircleEvent;
            }
        }
        return $query;
    }

    /**
     * @param array $arrEventList
     * @param $entityManager
     * @return array
     */
    public function getEventUpComingOrOnGoingDetails($arrEventList = array(), $entityManager) {
        if (empty($arrEventList))
            return array();
        $arrEventsDates = array();
        $objCurrentDate = new DateTime();
        $objCurrentDate->setTime('0','0');
        foreach ($arrEventList as $trnCircleEvents) {
            if (!empty($trnCircleEvents->getFromDate()) && empty($trnCircleEvents->getToDate())){
                $startDate = $trnCircleEvents->getFromDate();
                $endDate = $trnCircleEvents->getToDate();
                if($trnCircleEvents->getIsTargetAchieved() == 1) {
                    $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "PAST";
                } else {
                    if (!empty($startDate) && $startDate > $objCurrentDate) {
                        $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "UPCOMING";
                    } elseif (!empty($startDate) && !empty($endDate) && $startDate <= $objCurrentDate && $endDate >= $objCurrentDate) {
                        $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "ONGOING";
                    } else {
                        $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "PAST";
                    }
                }
                continue;
            }
            $trnVolunterCircleEventDetails = null;
            $trnVolunterCircleEventDetailsArray = $trnCircleEvents->getTrnVolunterCircleEventDetails();
            if (!empty($trnVolunterCircleEventDetailsArray) && !empty($trnVolunterCircleEventDetailsArray[0])) {
                $trnVolunterCircleEventDetails = $trnVolunterCircleEventDetailsArray[0];
            }
            $startDate = $endDate = null;
            $startTime = $endTime = null;
            if (!empty($trnCircleEvents->getFromDate()))
                $startDate = $trnCircleEvents->getFromDate();
            if (!empty($trnCircleEvents->getToDate()))
                $endDate = $trnCircleEvents->getToDate();

            if (!empty($trnVolunterCircleEventDetails)) {
                $startDate = $trnVolunterCircleEventDetails->getFromDate();
                $endDate = $trnVolunterCircleEventDetails->getToDate();
                if (!empty($trnVolunterCircleEventDetails->getFromTime()))
                    $startTime = $trnVolunterCircleEventDetails->getFromTime();
                if (!empty($trnVolunterCircleEventDetails->getToTime()))
                    $endTime = $trnVolunterCircleEventDetails->getToTime();
                if ($startTime == $endTime)
                {
                    $startTime = null;
                    $endTime = null;
                }
            }
            $trnMaterialInKindCircleEventDetails = null;
            $trnMaterialInKindCircleEventDetailsArray = $trnCircleEvents->getTrnMaterialInKindCircleEventDetails();
            if(!empty($trnMaterialInKindCircleEventDetailsArray) && !empty($trnMaterialInKindCircleEventDetailsArray[0])){
                $trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetailsArray[0];
            }
            if (!empty($trnMaterialInKindCircleEventDetails)) {
                if (!empty($startDate) && !empty($trnMaterialInKindCircleEventDetails->getFromDate()) &&
                $trnMaterialInKindCircleEventDetails->getFromDate() < $startDate){
                    $startDate = $trnMaterialInKindCircleEventDetails->getFromDate();
                } else if(!empty($trnMaterialInKindCircleEventDetails->getFromDate()) && empty($startDate)) {
                    $startDate = $trnMaterialInKindCircleEventDetails->getFromDate();
                }
                if (!empty($endDate) && !empty($trnMaterialInKindCircleEventDetails->getToDate()) &&
                    $trnMaterialInKindCircleEventDetails->getToDate() > $endDate){
                    $endDate = $trnMaterialInKindCircleEventDetails->getToDate();
                } elseif(!empty($trnMaterialInKindCircleEventDetails->getToDate()) && empty($endDate)) {
                    $endDate = $trnMaterialInKindCircleEventDetails->getToDate();
                }
                foreach ($trnMaterialInKindCircleEventDetails->getTrnMaterialInKindCircleEventCollectionCentres() as
                         $trnMaterialInKindCircleEventCollectionCentres) {
                    foreach ($trnMaterialInKindCircleEventCollectionCentres as $trnMaterialInKindCircleEventCollectionCentre) {
                        if (!empty($startTime) && !empty($trnMaterialInKindCircleEventCollectionCentre->getStartTime()) && $trnMaterialInKindCircleEventCollectionCentre->getStartTime
                            () > $startTime ){
                            $startTime = $trnMaterialInKindCircleEventCollectionCentre->getStartTime();
                        } elseif(!empty($trnMaterialInKindCircleEventCollectionCentre->getStartTime()) && empty($startTime)) {
                            $startTime = $trnMaterialInKindCircleEventCollectionCentre->getStartTime();
                        }
                        if (!empty($endTime) && !empty($trnMaterialInKindCircleEventCollectionCentre->getEndTime()) &&
                            $trnMaterialInKindCircleEventCollectionCentre->getEndTime() > $endTime ){
                            $endTime = $trnMaterialInKindCircleEventCollectionCentre->getEndTime();
                        } elseif(!empty($trnMaterialInKindCircleEventCollectionCentre->getEndTime()) && empty($endTime)) {
                            $endTime = $trnMaterialInKindCircleEventCollectionCentre->getEndTime();
                        }
                    }
                }

            }
            $trnFundRaiserCircleEventDetails = null;
            $trnFundRaiserCircleEventDetailsArray = $trnCircleEvents->getTrnFundRaiserCircleEventDetails();
            if(!empty($trnFundRaiserCircleEventDetailsArray) && !empty($trnFundRaiserCircleEventDetailsArray[0])){
                $trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetailsArray[0];
            }
            if (!empty($trnFundRaiserCircleEventDetails)) {
                if (!empty($startDate) && !empty($trnFundRaiserCircleEventDetails->getFromDate()) &&
                    $trnFundRaiserCircleEventDetails->getFromDate() < $startDate){
                    $startDate = $trnFundRaiserCircleEventDetails->getFromDate();
                } elseif(!empty($trnFundRaiserCircleEventDetails->getFromDate()) && empty($startDate)) {
                    $startDate = $trnFundRaiserCircleEventDetails->getFromDate();
                }

                if (!empty($endDate) && !empty($trnFundRaiserCircleEventDetails->getToDate()) &&
                    $trnFundRaiserCircleEventDetails->getToDate() > $endDate){
                    $endDate = $trnFundRaiserCircleEventDetails->getToDate();
                } elseif(!empty($trnFundRaiserCircleEventDetails->getToDate()) && empty($endDate)) {
                    $endDate = $trnFundRaiserCircleEventDetails->getToDate();
                }
            }

            $trnCircleEvents->setFromDate($startDate);
            $trnCircleEvents->setToDate($endDate);

            $entityManager->persist($trnCircleEvents);
            $arrEventsDates[$trnCircleEvents->getId()]['startDate'] = $startDate;
            $arrEventsDates[$trnCircleEvents->getId()]['endDate'] = $endDate;

            $arrEventsDates[$trnCircleEvents->getId()]['startTime'] = $startTime;
            $arrEventsDates[$trnCircleEvents->getId()]['endTime'] = $endTime;
            if($trnCircleEvents->getIsTargetAchieved() == 1) {
                $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "PAST";
            } else {
                if (!empty($startDate) && $startDate > $objCurrentDate) {
                    $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "UPCOMING";
                } elseif (!empty($startDate) && !empty($endDate) && $startDate <= $objCurrentDate && $endDate >= $objCurrentDate) {
                    $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "ONGOING";
                } else {
                    $arrEventsDates[$trnCircleEvents->getId()]["eventUpComingOrOnGoing"] = "PAST";
                }
            }
        }
        $entityManager->flush();
        return $arrEventsDates;
    }

    /**
     * @param MstStatus $objMstStatus
     * @param MstStatus $expiredObjMstStatus
     * @param $company_id
     * @param $arrMstEventProductType
     * @param array $arrInputParam
     * @return mixed
     */
    public function getFundRaiserAndCrowdFundingEvents(MstStatus $objMstStatus, $company_id, $arrMstEventProductType,
                                                       $arrInputParam = array(),MstStatus $expiredObjMstStatus = null) {

        $q = $this->createQueryBuilder('c');
        $q->andWhere('c.isActive = :active')
            ->andWhere('c.orgCompany = :company');

        if(!empty($arrInputParam['circleid'])) {

            $q->leftJoin('c.trnCircle','trc')
                ->andWhere('trc.id = :circleid')
                ->setParameter('circleid', $arrInputParam['circleid']);
        }

        if(!empty($arrInputParam['userid'])) {

            $q->innerJoin('c.appUser', 'u')
                ->andWhere('(u.id = :user_id )')
                ->setParameter('user_id', $arrInputParam['userid']);
        }
        if ($expiredObjMstStatus == null) {
            $q->andWhere('c.mstStatus = :status');
        } else {
            $q->andWhere('c.mstStatus = :status OR c.mstStatus = :expStatus')
                ->setParameter('expStatus', $expiredObjMstStatus->getId());
        }

        if(!empty($arrMstEventProductType)) {
            $q->innerJoin('c.mstEventProductType', 'p')
                ->andWhere('(p.id in (:mstEventProductType_id) )')
                ->setParameter('mstEventProductType_id', $arrMstEventProductType);
        }

        if(!empty($arrInputParam['statusChkBox'])) {
            foreach ($arrInputParam['statusChkBox'] as $statusChk) {
                if($statusChk == 'Urgent') {
                    $q->andWhere('c.isUrgent = :urgent')
                        ->setParameter('urgent', 1);
                }
                if($statusChk == '80G') {
                    $q->innerJoin('c.appUser', 'uc')
                        ->innerJoin('uc.appUserInfo', 'uca')
                        ->innerJoin('uca.mstUserMemberType', 'ucamt')
                        ->andWhere('ucamt.userMemberType = :org_member')
                        ->setParameter('org_member', 'Organization');
                }
            }
        }

        if (!empty($arrInputParam['searchText']) && !empty($arrInputParam['searchCity']) && $arrInputParam['searchText'] != $arrInputParam['searchCity']) {
            if (!empty($arrInputParam['searchText'])) {
                $searchText = $arrInputParam['searchText'];
                $strCondition = " c.name like :search_text OR tc.circle like :search_text ";
                $q->leftJoin('c.trnCircle','tc')
                    ->andWhere($strCondition)
                    ->setParameter('search_text', "%".$searchText."%");
            }

            if (!empty($arrInputParam['searchCity'])) {
                $searchCity = $arrInputParam['searchCity'];
                $strCondition = " mc.city like :search_city OR ms.state like :search_city OR mt.country like :search_city";
                $q->leftJoin('c.mstCity', 'mc')
                    ->leftJoin('c.mstState', 'ms')
                    ->leftJoin('c.mstCountry', 'mt')
                    ->andWhere($strCondition)
                    ->setParameter('search_city', "%".$searchCity."%");
            }
        } else {
            if (!empty($arrInputParam['searchText'])) {
                $searchText = $arrInputParam['searchText'];
                $strCondition = " c.name like :search_text OR tcs.circle like :search_text ";
                $q->leftJoin('c.trnCircle','tcs')
                    ->andWhere($strCondition)
                    ->setParameter('search_text', "%".$searchText."%");
            }
            if (!empty($arrInputParam['searchCity'])) {
                $searchCity = $arrInputParam['searchCity'];
                $strCondition = " c.name like :search_city OR tc.circle like :search_city OR mc.city like :search_city OR ms.state like :search_city OR mt.country like :search_city";
                $q->leftJoin('c.trnCircle','tc')
                    ->leftJoin('c.mstCity', 'mc')
                    ->leftJoin('c.mstState', 'ms')
                    ->leftJoin('c.mstCountry', 'mt')
                    ->andWhere($strCondition)
                    ->setParameter('search_city', "%".$searchCity."%");
            }
        }
        if (!empty($arrInputParam['areaOfInterestChkBox'])) {
            $interestId = $arrInputParam['areaOfInterestChkBox'];
            $q->innerJoin('c.trnAreaOfInterests', 't')
                ->innerJoin('t.areaInterestPrimary','i')
                ->andWhere('i.id in (:interest) ')
                ->setParameter('interest', $interestId);
        }
        if (!empty($arrInputParam['joinBy'])) {
            $joinById = $arrInputParam['joinBy'];
            $q->innerJoin('c.mstJoinBy', 'j')
                ->andWhere('j.id in (:joinBy) ')
                ->setParameter('joinBy', $joinById);
        }
        $strCurrentDate = date('Y-m-d');
        if (!empty($arrInputParam['eventTime'])) {
            if(count($arrInputParam['eventTime']) < 3) {
                $arrCondition = array();
                foreach ($arrInputParam['eventTime'] as $strEventTime) {
                    switch (strtolower($strEventTime)) {
                        case 'upcoming':{
                            $arrCondition[] = " c.fromDate > :current_date";
                            break;
                        }
                        case 'ongoing':{
                            $arrCondition[] = " (c.fromDate <= :current_date and c.toDate >= :current_date )";
                            break;
                        }
                        case 'past':{
                            $arrCondition[] = " c.toDate < :current_date OR c.isTargetAchieved = 1";
                            break;
                        }
                    }
                }
                $strFinalQuery = implode(' OR ', $arrCondition);
                $q->andWhere($strFinalQuery)
                    ->setParameter('current_date', $strCurrentDate);
            }
        }

        $q->setParameter('active', 1)
            ->setParameter('company', $company_id)
            ->setParameter('status', $objMstStatus->getId())
            ->orderBy('c.createdOn','DESC');
        $query = $q->getQuery();

        /*echo '<pre>';
        print_r($query->getParameters());
        dd($query->getSQL());*/

        return $query->getResult();
    }

    public function changeEventStatus() {

        $cnt = 0;
        $qb = $this->createQueryBuilder('e');
        $q = $qb->update('App\Entity\Transaction\TrnCircleEvents', 'e')
            ->set('e.mstStatus', '?1')
            ->andWhere("e.mstStatus IN (Select mst.id from App\Entity\Master\MstStatus mst where mst.status IN ('Activated', 'Pending Activation'))")
            ->andWhere('e.toDate < :date')
            ->setParameter('date', new \DateTime())
            ->setParameter(1, '5')
            ->getQuery();

        $cnt = $q->execute();
        return $cnt;
    }

    public function countOldEvents(): int
    {
        try {
            return $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
//                ->andWhere("e.mstStatus IN (Select mst.id from App\Entity\Master\MstStatus mst where mst.status IN ('Activated', 'Pending Activation'))")
                ->andWhere("e.mstStatus = :activeStatus")
                ->andWhere('e.toDate < :date')
                ->setParameter('date', new \DateTime())
                ->setParameter('activeStatus', 'Activated')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getFundsEventByUser($userId) {
        $sql = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.mstStatus', 'ms')
            ->innerJoin('e.trnFundRaiserCircleEventDetails', 'fre')
            ->andWhere('fre.trnCircleEvents = e.id')
            ->andWhere("ms.status IN ('Activated', 'Expired')")
            ->andWhere("e.appUser = :userId")
            ->andWhere("e.isActive = :active")
            ->setParameter('userId', $userId)
            ->setParameter('active', 1)
            ->getQuery();

        return $sql->getSingleScalarResult();
    }

    /**
     * @param $arrTrnCircleEvents
     * @param $arrParameters
     * @return
     */
    public function getDistributedEventMultiEvents($arrTrnCircleEvents, $arrParameters) {
        $arrId = array();
        foreach ($arrTrnCircleEvents as $trnCircleEvent) {
            $arrId[] = $trnCircleEvent->getId();
        }
        $q = $this->createQueryBuilder('c');
        $q->andWhere('c.isActive = :active')
            ->innerJoin('c.parentTrnCircleEvents','pe')
            ->andWhere('pe.id in (:parentEventId)')
            ->setParameter('parentEventId', $arrId);
        if (!empty($arrParameters)) {
            if (!empty($arrParameters['quicksearch'])) {
                $q->innerJoin('c.trnCircle', 'tc')
                    ->andWhere('c.name like :quicksearch OR tc.circle like :quicksearch')
                    ->setParameter('quicksearch', "%$arrParameters[quicksearch]%");
            }
            if (!empty($arrParameters['from']) && !empty($arrParameters['to']) ) {
                $q->andWhere('(c.fromDate >= :fromdate AND c.fromDate <= :todate ) OR (c.toDate >= :fromdate AND c.toDate <= :todate )')
                    ->setParameter('fromdate', $arrParameters['from'])
                    ->setParameter('todate', $arrParameters['to']);
            }
        }
        $q->setParameter('active', 1)
            ->orderBy('c.createdOn','DESC');
        $query = $q->getQuery() ;
        return $query->getResult();
    }
}
