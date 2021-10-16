<?php

namespace App\Twig\Common;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsPressRoom;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstArticleCategory;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use \App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Transaction\TrnCircleEventRequestToParticipate;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCircleRequestToJoin;
use App\Entity\Transaction\TrnOrder;
use App\Repository\Master\MstNotificationStatusRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnCircleRequestToJoinRepository;
use App\Repository\Transaction\TrnNotificationsRepository;
use App\Service\CommonHelper;
use App\Service\MyAccountService;
use Container1lm0GPH\getDoctrine_DatabaseDropCommandService;
use Container4hKkA7y\getTrnCircleEventsCrowdFundEventPortalTypeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CommonExtension extends AbstractExtension
{
    private $em;
    private $params;
    private $security;
    private $commonHelper;

    protected $myAccoutService;

    /**
     * @var TrnNotificationsRepository
     */
    private $trnNotificationsRepository;

    /**
     * @var MstStatusRepository
     */
    private $mstStatusRepository;

    /**
     * @var MstNotificationStatusRepository
     */
    private $mstNotificationStatusRepository;

    /**
     * @var
     */
    private $appUserRepository;

    /**
     * @var TrnCircleRequestToJoinRepository
     */
    private $trnCircleRequestToJoinRepository;

    /**
     * @var TrnCircleRepository
     */
    private $trnCircleRepository;

    /**
     * CommonExtension constructor.
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $params
     * @param Security $security
     * @param TrnNotificationsRepository $trnNotificationsRepository
     * @param MstStatusRepository $mstStatusRepository
     * @param MstNotificationStatusRepository $mstNotificationStatusRepository
     * @param AppUserRepository $appUserRepository
     * @param TrnCircleRequestToJoinRepository $trnCircleRequestToJoinRepository
     * @param TrnCircleRepository $trnCircleRepository
     * @param MyAccountService $myAccountService
     * @param CommonHelper $commonHelper
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params, Security $security,
                                TrnNotificationsRepository $trnNotificationsRepository, MstStatusRepository
                                $mstStatusRepository, MstNotificationStatusRepository
                                $mstNotificationStatusRepository, AppUserRepository $appUserRepository, TrnCircleRequestToJoinRepository
                                $trnCircleRequestToJoinRepository, TrnCircleRepository $trnCircleRepository,
                                MyAccountService $myAccountService, CommonHelper $commonHelper)
    {
        $this->em = $em;
        $this->params = $params;
        $this->security = $security;
        $this->trnNotificationsRepository = $trnNotificationsRepository;
        $this->mstStatusRepository = $mstStatusRepository;
        $this->mstNotificationStatusRepository = $mstNotificationStatusRepository;
        $this->appUserRepository = $appUserRepository;
        $this->trnCircleRequestToJoinRepository = $trnCircleRequestToJoinRepository;
        $this->trnCircleRepository = $trnCircleRepository;
        $this->myAccoutService = $myAccountService;
        $this->commonHelper = $commonHelper;
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('filter_name', [$this, 'doSomething']),
            new TwigFilter('phone_filter', [$this, 'phoneFilter']),
            new TwigFilter('ucwords', [$this, 'ucwordsFilter']),
        ];
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_country_list', [$this, 'getCountryList']),
            new TwigFunction('get_parameter', [$this, 'getParameter']),
            new TwigFunction('get_circle_list', [$this, 'getCircleList']),
            new TwigFunction('get_count_data', [$this, 'getCountData']),
            new TwigFunction('get_circle_event_count_by_interest', [$this, 'getCircleEventCountByInterest']),
            new TwigFunction('get_latest_circle_events_by_interest', [$this, 'getLatestCircleEventsByInterest']),
            new TwigFunction('get_latest_circle_events', [$this, 'getLatestCircleEvents']),
            new TwigFunction('get_latest_circle_events_by_user', [$this, 'getLatestCircleEventsByUser']),
            new TwigFunction('get_trending_circle_events', [$this, 'getTrendingCircleEvents']),
            new TwigFunction('get_circle_count_data', [$this, 'getCircleCountData']),
            new TwigFunction('get_events_dates', [$this, 'getEventsDates']),
            new TwigFunction('set_circle_read', [$this, 'setCircleRead']),
            new TwigFunction('get_circle_comments', [$this, 'getCircleComments']),
            new TwigFunction('num_to_words', [$this, 'numToWords']),
            new TwigFunction('crowd_funding_data', [$this, 'crowdFundingData']),
            new TwigFunction('get_gc_email', [$this, 'getGcEmail']),
            new TwigFunction('get_whatsapp_number', [$this, 'getWhatsappNumber']),
            new TwigFunction('get_fund_event_count', [$this, 'getFundEventCount']),
            new TwigFunction('get_user_join_status', [$this, 'getUserJoinStatus']),
            new TwigFunction('get_fund_event_id', [$this, 'getFundEventId']),
            new TwigFunction('get_pending_request_status', [$this, 'getPendingRequestStatus']),
            new TwigFunction('get_event_count_data', [$this, 'getEventCountData']),
            new TwigFunction('get_valid_project_buttons', [$this, 'getValidProjectButtons']),
            new TwigFunction('get_event_comment_count', [$this, 'getEventCommentCount']),
            new TwigFunction('get_valid_event_buttons', [$this, 'getValidEventButtons']),
            new TwigFunction('get_unread_notification_data', [$this, 'getUnreadNotificationData']),
            new TwigFunction('get_request_to_join_status', [$this, 'getRequestToJoinStatus']),
            new TwigFunction('check_if_all_mandatory_filled', [$this, 'checkIfAllMandatoryFilled']),
            new TwigFunction('get_change_maker_by_user', [$this, 'getChangeMakerByUser']),
            new TwigFunction('get_secondary_area_of_interest', [$this, 'getSecondaryAreaOfInterest']),
            new TwigFunction('get_days_list', [$this, 'getDaysList']),
            new TwigFunction('get_active_news_count', [$this, 'getActiveNewsCount']),
        ];
    }

    /**
     * @return MstCountry[]|object[]
     */
    public function getCountryList()
    {
        return $this->em->getRepository(MstCountry::class)->findAll();
    }

    public function getParameter($parameter)
    {
        return $this->params->get($parameter);

    }

    /**
     * @return mixed
     */
    public function getCircleList()
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        return $this->em->getRepository(TrnCircle::class)->findBy(array('orgCompany' =>
            $objOrgCompany));
    }

    /**
     * @return mixed
     */
    public function getCountData()
    {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objExpiredMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $arrCountData = array('circle' => count($this->em->getRepository(TrnCircle::class)->findBy(
            array('orgCompany' => $objOrgCompany, 'mstStatus' => $objMstStatus, 'isActive' => 1))));
        $em = $this->em;
//        $arrMstEventProductType = $this->em->getRepository(MstEventProductType::class)->findAll();
        $arrMstEventProductType = $this->em->getRepository(MstEventProductType::class)->findBy(["isActive" => true]);
        foreach ($arrMstEventProductType as $MstEventProductType) {
            if ($MstEventProductType->getEventProductType() == 'Crowdfunding')
                continue;
            $arrProductType[] = $MstEventProductType->getId();
        }
        $strEventName = "";
        $repository = $em->getRepository(TrnCircleEvents::class);
        foreach ($arrMstEventProductType as $objMstEventProductType) {
            if (stripos($objMstEventProductType->getEventProductType(), 'time') !== false)
                $strEventName = $objMstEventProductType->getEventProductType();
            $query = $repository->createQueryBuilder('e')
                ->innerJoin('e.mstEventProductType', 'p')
                ->where('e.orgCompany = :orgCompany_id')
                ->andWhere('p.id = :mstEventProductType_id')
                ->andWhere('e.mstStatus = :mstStatus1_id OR e.mstStatus = :mstExpiredStatus1')
                ->andWhere('e.isActive = :active')
                ->setParameter('orgCompany_id', $objOrgCompany)
                ->setParameter('mstEventProductType_id', $objMstEventProductType)
                ->setParameter('mstStatus1_id', $objMstStatus)
                ->setParameter('mstExpiredStatus1', $objExpiredMstStatus)
                ->setParameter('active', 1)
                ->getQuery()->getResult();
            $strEventProductType = str_ireplace(array("(", ")", " "), array("", "", ""), strtolower
            ($objMstEventProductType->getEventProductType()));
            $arrCountData[$strEventProductType] = count($query);
        }

        $query = $repository->createQueryBuilder('e')
            ->select("count(distinct(e.id))")
            ->innerJoin('e.mstEventProductType', 'p')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('p.id IN (:mstEventProductType_id)')
            ->andWhere('e.mstStatus = :mstStatus1_id OR e.mstStatus = :mstStatusExpired1_id')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('mstEventProductType_id', $arrProductType)
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->setParameter('mstStatusExpired1_id', $objExpiredMstStatus)
            ->setParameter('active', 1)
            ->getQuery()->getResult();

        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCountData["totalEvents"] = $query[0][1];
        }

        //Get Count for Article Read
        $repository = $em->getRepository(CmsArticle::class);
        $query = $repository->createQueryBuilder('a')
            ->select('sum(a.articleViewCount)')
            ->where('a.orgCompany = :orgCompany_id')
            ->andWhere('a.isActive = 1')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->getQuery()->getResult();
        $arrCountData["articleViewCount"] = 0;
        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCountData["articleViewCount"] = $query[0][1];
        }

        //Get Member Across Project/Circle
        $repository = $em->getRepository(TrnCircleRequestToJoin::class);
        $queryMem = $repository->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.mstStatus = :mstStatus_id')
            ->andWhere('r.isActive = :active')
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('active', 1)
            ->getQuery()->getResult();

        if (!empty($queryMem) && !empty($queryMem[0]) && !empty($queryMem[0][1])) {
            $arrCountData["memberCount"] = $queryMem[0][1];
        } else {
            $arrCountData["memberCount"] = 0;
        }

        //No of Users who have created project
        $arrCountData["changerMakerCount"] = 0;
        $repository = $em->getRepository(TrnCircle::class);
        $query = $repository->createQueryBuilder('c')
            ->select('count( distinct u.id)')
            ->innerJoin('c.appUser', 'u')
            ->where('c.orgCompany = :orgCompany_id')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->getQuery()->getResult();
        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCountData["changerMakerCount"] = $query[0][1];
        }

        //Total Fund Raised
        $orderRepository = $this->em->getRepository(TrnOrder::class);
        $sql = $orderRepository->createQueryBuilder('o')
            ->select('sum(o.transactionAmount) as totalTransactionAmount')
            ->innerJoin('o.trnCircleEvent', 'e')
            ->andWhere('e.isActive = 1')
            ->andWhere('o.transactionStatus = :transactionStatus')
            ->setParameter('transactionStatus', 'success')
            ->getQuery();

        $query = $sql->getResult();
        $totalAmt = 0;
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['totalTransactionAmount'])) {
            $totalAmt = $query[0]['totalTransactionAmount'];
        }
        $lacConvertor = 100000;
        $arrCountData['totalFundRaised'] = round($totalAmt / $lacConvertor, 2);

        //Total Volunter Events Hour
        $arrCountData["totalEventHrs"] = 0;
        $objMstEventProductType = $this->em->getRepository(MstEventProductType::class)->findOneBy(array('eventProductType' => $strEventName));
        if (!empty($objMstEventProductType)) {
            $repository = $em->getRepository(TrnCircleEvents::class);
            $query = $repository->createQueryBuilder('e')
                ->innerJoin('e.mstEventProductType', 'p')
                ->where('e.orgCompany = :orgCompany_id')
                ->andWhere('p.id = :mstEventProductType_id')
                ->andWhere('e.mstStatus = :mstStatus1_id OR e.mstStatus = :mstStatusExpired1_id')
                ->setParameter('orgCompany_id', $objOrgCompany)
                ->setParameter('mstEventProductType_id', $objMstEventProductType)
                ->setParameter('mstStatus1_id', $objMstStatus)
                ->setParameter('mstStatusExpired1_id', $objExpiredMstStatus)
                ->getQuery()->getResult();

            $nMyProjectHrs = 0;
            foreach ($query as $trnCircleEvent) {
                foreach ($trnCircleEvent->getTrnVolunteerCircleParticipationDetails() as $trnVolunteerCircleParticipationDetail) {

                    /* Removed status condition as QR code is not in place */
//                    if ($trnVolunteerCircleParticipationDetail->getMstStatus() == $objMstStatus)
                        $nMyProjectHrs += $trnVolunteerCircleParticipationDetail->getNumberOfHours();
                }
            }
            $arrCountData["totalEventHrs"] = $nMyProjectHrs;
        }

        return $arrCountData;
    }

    /**
     * @param $nInterest
     * @return int
     */
    public function getCircleEventCountByInterest($nInterest): int
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $repository = $em->getRepository(TrnCircleEvents::class);
        $objMstAreaInterest = $this->em->getRepository(MstAreaInterest::class)->find($nInterest);
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objExpiredMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $query = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.trnCircle', 'c')
            ->innerJoin('c.trnAreaOfInterests', 't')
            ->innerJoin('t.areaInterestPrimary', 'i')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('i.id = :areaIntereste_id')
            ->andWhere('c.mstStatus = :mstStatus_id')
            ->andWhere('e.mstStatus = :mstStatus1_id OR e.mstStatus = :mstStatusExpired1_id')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('areaIntereste_id', $objMstAreaInterest)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->setParameter('mstStatusExpired1_id', $objExpiredMstStatus)
            ->setParameter('active', 1)
            ->getQuery()->getResult();
        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            return $query[0][1];
        } else {
            return 0;
        }
    }

    /**
     * @param $nInterest
     * @return mixed
     */
    public function getLatestCircleEventsByInterest($nInterest)
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $repository = $em->getRepository(TrnCircleEvents::class);
        $objMstAreaInterest = $this->em->getRepository(MstAreaInterest::class)->find($nInterest);
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objExpiredMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $query = $repository->createQueryBuilder('e')
            ->innerJoin('e.trnCircle', 'c')
            ->innerJoin('c.trnAreaOfInterests', 't')
            ->innerJoin('t.areaInterestPrimary', 'i')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('i.id = :areaInterest_id')
            ->andWhere('c.mstStatus = :mstStatus_id')
            ->andWhere('e.mstStatus = :mstStatus1_id OR e.mstStatus = :mstStatusExpired1_id')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('areaInterest_id', $objMstAreaInterest)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->setParameter('mstStatusExpired1_id', $objExpiredMstStatus)
            ->setParameter('active', 1)
            ->orderBy('e.createdOn', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();

        return $query;
    }

    /**
     * @param $nCircleId
     * @param null $eventId
     * @return mixed
     */
    public function getLatestCircleEvents($nCircleId, $eventId = null)
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $trnCircle = $this->em->getRepository(TrnCircle::class)->findBy(array('orgCompany' => $objOrgCompany, 'id' =>
            $nCircleId));
        if($eventId != null) {
            $objTrnCircleEvent = $this->em->getRepository(TrnCircleEvents::class)->find($eventId);
        }
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objExpiredMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $repository = $em->getRepository(TrnCircleEvents::class);
        $query = $repository->createQueryBuilder('e')
            ->innerJoin('e.trnCircle', 'c')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('c.id = :circle_id')
            ->andWhere('e.mstStatus = :mstStatus_id OR e.mstStatus = :mstStatusExpired_id')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('circle_id', $trnCircle)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('mstStatusExpired_id', $objExpiredMstStatus)
            ->setParameter('active' , 1);

        if($eventId != null) {
            $query->andWhere('e.id != :event_id')
                ->andWhere('e.parentTrnCircleEvents IS NULL')
                ->setParameter('event_id', $objTrnCircleEvent);
        }

        return $query->orderBy('e.createdOn', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();
    }

    /**
     * @param $nCircleId
     * @param null $eventId
     * @return mixed
     */
    public function getTrendingCircleEvents($nCircleId, $eventId = null)
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $trnCircle = $this->em->getRepository(TrnCircle::class)->findBy(array('orgCompany' => $objOrgCompany, 'id' =>
            $nCircleId));
        if($eventId != null) {
            $objTrnCircleEvent = $this->em->getRepository(TrnCircleEvents::class)->find($eventId);
        }
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $repository = $em->getRepository(TrnCircleEvents::class);
        $query = $repository->createQueryBuilder('e')
            ->innerJoin('e.trnCircle', 'c')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('c.id = :circle_id')
            ->andWhere('e.mstStatus = :mstStatus_id')
            ->andWhere('e.isTrending = :trending')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('circle_id', $trnCircle)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('trending', 1)
            ->setParameter('active' , 1);

        if($eventId != null) {
            $query->andWhere('e.id != :event_id')
                ->andWhere('e.parentTrnCircleEvents IS NULL')
                ->setParameter('event_id', $objTrnCircleEvent);
        }

        return $query->orderBy('e.createdOn', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();
    }

    /**
     * @param $objUser
     * @param null $eventId
     * @return mixed
     */
    public function getLatestCircleEventsByUser($objUser, $eventId = null)
    {
        $em = $this->em;
        $objOrgCompany = $em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        if($eventId != null) {
            $objTrnCircleEvent = $em->getRepository(TrnCircleEvents::class)->find($eventId);
        }
        $objMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objExpiredMstStatus = $em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $repository = $em->getRepository(TrnCircleEvents::class);
        $query = $repository->createQueryBuilder('e')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('e.appUser = :user_id')
            ->andWhere('e.mstStatus = :mstStatus_id OR e.mstStatus = :mstStatusExpired_id')
            ->andWhere('e.isActive = :active')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('user_id', $objUser)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('mstStatusExpired_id', $objExpiredMstStatus)
            ->setParameter('active' , 1);

        if($eventId != null) {
            $query->andWhere('e.id != :event_id')
                ->andWhere('e.parentTrnCircleEvents IS NULL')
                ->setParameter('event_id', $objTrnCircleEvent);
        }

        return $query->orderBy('e.createdOn', 'DESC')
            ->setMaxResults(3)
            ->getQuery()->getResult();
    }

    /**
     * @param $nCircleId
     * @return array
     */
    public function getCircleCountData($nCircleId): array
    {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $trnCircle = $this->em->getRepository(TrnCircle::class)->findOneBy(array('orgCompany' => $objOrgCompany, 'id' =>
            $nCircleId));
        $repository = $em->getRepository(TrnCircleEvents::class);
        $query = $repository->createQueryBuilder('e')
            ->select('count(distinct(e.id))')
            ->innerJoin('e.trnCircle', 'c')
            ->innerJoin('e.mstEventProductType', 'p')
            ->andWhere("p.eventProductType IN ('Volunteer (in Time)','Material (in Kind)','Fundraiser')")
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('c.id = :circle_id')
            ->andWhere('e.mstStatus = :mstStatus_id')
            ->andWhere('c.mstStatus = :mstStatus1_id')
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('circle_id', $trnCircle)
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->getQuery()->getResult();
        $arrCircleCount = array("eventCount" => 0, "memberCount" => "0", "readCount" => $trnCircle->getReadCount(),
            "likeCount" => $trnCircle->getLikeCount(), "commentCount" => "0", "totalCircleHrs" => "0", "totalFundRaised" =>
                "0", "shareCount" => $trnCircle->getShareCount());
        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCircleCount["eventCount"] = $query[0][1];
        } else {
            $arrCircleCount["eventCount"] = 0;
        }

        // Member count
        $repository = $em->getRepository(TrnCircleRequestToJoin::class);
        $queryMem = $repository->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.trnCircle = :circle_id')
            ->andWhere('r.mstStatus = :mstStatus_id')
            ->setParameter('mstStatus_id', $objMstStatus)
            ->setParameter('circle_id', $trnCircle)
            ->getQuery()->getResult();

        if (!empty($queryMem) && !empty($queryMem[0]) && !empty($queryMem[0][1])) {
            $arrCircleCount["memberCount"] = $queryMem[0][1];
        } else {
            $arrCircleCount["memberCount"] = 0;
        }

        // Comment count
        $repository = $em->getRepository(TrnCircleEventComments::class);
        $query = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.trnCircle', 'c')
            ->andWhere('c.id = :circle_id')
            ->andWhere('e.trnCircleEvents is null')
//            ->andWhere('e.parentComment is null') // reply will also be counted in comment count
            ->andWhere('e.isApproved = 1')
            ->setParameter('circle_id', $trnCircle)
            ->getQuery()->getResult();

        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCircleCount["commentCount"] = $query[0][1];
        } else {
            $arrCircleCount["commentCount"] = 0;
        }

        // Bifurcated counts of event types
        $arrMstEventProductType = $this->em->getRepository(MstEventProductType::class)->findAll();
        $repository = $em->getRepository(TrnCircleEvents::class);
        foreach ($arrMstEventProductType as $objMstEventProductType) {
            $query = $repository->createQueryBuilder('e')
                ->select('count(e.id)')
                ->innerJoin('e.trnCircle', 'c')
                ->innerJoin('e.mstEventProductType', 'p')
                ->where('e.orgCompany = :orgCompany_id')
                ->andWhere('c.id = :circle_id')
                ->andWhere('p.id = :mstEventProductType_id')
                ->andWhere('c.mstStatus = :mstStatus_id')
                ->andWhere('e.mstStatus = :mstStatus1_id')
                ->setParameter('circle_id', $trnCircle)
                ->setParameter('mstStatus_id', $objMstStatus)
                ->setParameter('orgCompany_id', $objOrgCompany)
                ->setParameter('mstEventProductType_id', $objMstEventProductType)
                ->setParameter('mstStatus1_id', $objMstStatus)
                ->getQuery()->getResult();
            $strEventProductType = str_ireplace(array("(", ")", " "), array("", "", ""), strtolower
            ($objMstEventProductType->getEventProductType()));

            if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
                $arrCircleCount[$strEventProductType] = $query[0][1];
            } else {
                $arrCircleCount[$strEventProductType] = 0;
            }
        }

        // Total funds raised
        $orderRepository = $this->em->getRepository(TrnOrder::class);
        $sql = $orderRepository->createQueryBuilder('o')
            ->select('sum(o.transactionAmount) as totalTransactionAmount')
            ->innerJoin('o.trnCircleEvent', 'e')
            ->innerJoin('e.trnCircle', 'c')
            ->andWhere('c.id = :circle_id')
            ->andWhere('e.isActive = 1')
            ->andWhere('o.transactionStatus = :transactionStatus')
            ->setParameter('circle_id', $nCircleId)
            ->setParameter('transactionStatus', 'success')
            ->getQuery();

        $query = $sql->getResult();
        $totalAmt = 0;
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['totalTransactionAmount'])) {
            $totalAmt = $query[0]['totalTransactionAmount'];
        }
        $lacConvertor = 100000;
        $arrCircleCount['totalFundRaised'] = $totalAmt / $lacConvertor;

        ################## Hours Calculation ###########################
        $nMyProjectHrs = 0;
        foreach ($trnCircle->getTrnCircleEvents() as $trnCircleEvent) {
            foreach ($trnCircleEvent->getTrnVolunteerCircleParticipationDetails() as $trnVolunteerCircleParticipationDetail) {

                /* TODO: Removed status condition as QR code is not in place */
                //if ( $trnVolunteerCircleParticipationDetail->getMstStatus() == $objMstStatus)
                    $nMyProjectHrs += $trnVolunteerCircleParticipationDetail->getNumberOfHours();
            }
        }
        $arrCircleCount['totalCircleHrs'] = $nMyProjectHrs;
        return $arrCircleCount;
    }

    /**
     * @param null $nEventId
     * @return string[]
     */
    public function getEventsDates($nEventId = null)
    {
        $eventData = $trnCircle = $this->em->getRepository(TrnCircleEvents::class)->findOneBy(array('id' =>
            $nEventId));
        $arrEventsDates = array("fromDate" => "", "toDate" => "", "eventUpComingOrOnGoing" => "", "eventFromTime" => "", "eventToTime" => "");
        $objEventPrdTypeData = null;
        $arrEventsDates["fromDate"] = $eventData->getFromDate();
        $arrEventsDates["toDate"] = $eventData->getToDate();

        if (count($eventData->getMstEventProductType()) == 1) {
            $strEventProductType = $eventData->getMstEventProductType()[0];
            $strEventProductType = str_ireplace(array("(", ")", " "), array("", "", ""), strtolower($strEventProductType));
            switch ($strEventProductType) {
                case 'volunteerintime':
                {
                    $objEventPrdTypeData = $eventData->getTrnVolunterCircleEventDetails()[0];
                    break;
                }
            }
        } else {
            if (count($eventData->getTrnVolunterCircleEventDetails()) > 0) {
                $objEventPrdTypeData = $eventData->getTrnVolunterCircleEventDetails()[0];
            }
        }
        if (!empty($objEventPrdTypeData)) {
            $arrEventsDates['eventFromTime'] = $objEventPrdTypeData->getFromTime();
            $arrEventsDates['eventToTime'] = $objEventPrdTypeData->getToTime();
        }
        $objCurrentDate = new DateTime();
        if (!empty($arrEventsDates['fromDate']) && $arrEventsDates['fromDate'] > $objCurrentDate) {
            $arrEventsDates["eventUpComingOrOnGoing"] = "UPCOMING";
        } elseif (!empty($arrEventsDates['fromDate']) && !empty($arrEventsDates['toDate']) &&
            $arrEventsDates['fromDate'] <= $objCurrentDate && $arrEventsDates['toDate'] >= $objCurrentDate) {
            $arrEventsDates["eventUpComingOrOnGoing"] = "ONGOING";
        } else {
            $arrEventsDates["eventUpComingOrOnGoing"] = "PAST";
        }

        $repository = $this->em->getRepository(TrnCircleEventComments::class);
        $query = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.trnCircleEvents', 'c')
            ->andWhere('c.id = :event_id')
            ->andWhere('e.isApproved = 1')
            ->andWhere('e.isActive = 1')
            ->setParameter('event_id', $eventData)
            ->getQuery()->getResult();
        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrEventsDates["commentCount"] = $query[0][1];
        } else {
            $arrEventsDates["commentCount"] = 0;
        }

        return $arrEventsDates;
    }

    /**
     * @param $nCircleId
     * @return int
     */
    public function setCircleRead($nCircleId)
    {
        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;
        $trnCircle = $this->em->getRepository(TrnCircle::class)->findOneBy(array('orgCompany' => $objOrgCompany, 'id' =>
            $nCircleId));
        if (!empty($trnCircle)) {
            $readCount = $trnCircle->getReadCount();
            if (empty($readCount)) {
                $readCount = 0;
            }
            $readCount++;
            $trnCircle->setReadCount($readCount);
            $em->persist($trnCircle);
            $em->flush();
            return 1;
        }
        return 0;
    }

    /**
     * @param $nCircleId
     * @param int $is_approved
     * @return array
     */
    public function getCircleComments($nCircleId, $is_approved = 1, $event_id = null)
    {
        if ($event_id != null) {
            $trnCircleEventComments = $this->em->getRepository(TrnCircleEventComments::class)->findBy([
                'trnCircleEvents' => $event_id, 'isApproved' => $is_approved], ['commentDateTime' => 'DESC']);

        } else {
            $trnCircleEventComments = $this->em->getRepository(TrnCircleEventComments::class)->findBy([
                'trnCircle' => $nCircleId, 'trnCircleEvents' => null, 'isApproved' => $is_approved], ['commentDateTime' => 'DESC']);
        }

        $arrComments = array();
        if (!empty($trnCircleEventComments)) {
            foreach ($trnCircleEventComments as $comment) {

                $userDispName = '';
                $userImage = '';
                $userInitials = '';

                // If logged in user
                if($comment->getAppUser() != null) {
                    if ($comment->getAppUser()->getAppUserInfo()->getUserFirstName() != '') {
                        $userDispName .= $comment->getAppUser()->getAppUserInfo()->getUserFirstName() . ' ';
                    }
                    if ($comment->getAppUser()->getAppUserInfo()->getUserLastName() != '') {
                        $userDispName .= $comment->getAppUser()->getAppUserInfo()->getUserLastName() . ' ';
                    }
                    if(trim($comment->getAppUser()->getAppUserInfo()->getUserAvatarImage()) != '' &&
                        $comment->getAppUser()->getAppUserInfo()->getUserAvatarImage() != null) {
                        $userImage = $comment->getAppUser()->getAppUserInfo()->getUserAvatarImagePath();
                    }
                }
                // If anonymous user
                if(trim($userDispName) == '') {
                    $userDispName = $comment->getCommentorName();
                }
                // If no image set then use initials
                if(trim($userImage) == '') {
                    $userInitialsArr = explode(' ', $userDispName);
                    foreach ($userInitialsArr as $str) {
                        $userInitials.= ''.substr($str, 0, 1);
                    }
                }

                if (!empty($comment->getParentComment())) {
                    $parentId = $comment->getParentComment()->getId();
                    $id = $comment->getId();
                    $arrComments['reply'][$parentId][$id]['parentId'] = $parentId;
                    $arrComments['reply'][$parentId][$id]['comment'] = $comment->getComment();
                    $arrComments['reply'][$parentId][$id]['name'] = $comment->getCommentorName();
                    $arrComments['reply'][$parentId][$id]['createtime'] = $comment->getCommentDateTime();
                    $arrComments['reply'][$parentId][$id]['likeCount'] = $comment->getLikeCount();
                    $arrComments['reply'][$parentId][$id]['name'] = trim($userDispName);
                    $arrComments['reply'][$parentId][$id]['userimage'] = $userImage;
                    $arrComments['reply'][$parentId][$id]['userinitials'] = $userInitials;
                }
                if (empty($comment->getParentComment())) {
                    $id = $comment->getId();
                    $arrComments['comment'][$id]['id'] = $comment->getId();
                    $arrComments['comment'][$id]['comment'] = $comment->getComment();
                    $arrComments['comment'][$id]['name'] = $comment->getCommentorName();
                    $arrComments['comment'][$id]['createtime'] = $comment->getCommentDateTime();
                    $arrComments['comment'][$id]['likeCount'] = $comment->getLikeCount();
                    $arrComments['comment'][$id]['name'] = trim($userDispName);
                    $arrComments['comment'][$id]['userimage'] = $userImage;
                    $arrComments['comment'][$id]['userinitials'] = $userInitials;
                }
            }
        }
        return $arrComments;
    }

    /**
     * @param $num
     * @return string
     */
    public function numToWords($num) {
        $ones = array(
            0 => "",
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
            19 => "nineteen"
        );
        return $ones[$num];
    }

    /**
     * @param $phoneNumber
     * @return string|string[]|null
     */
    public function phoneFilter($phoneNumber) {
        if (substr($phoneNumber, 0, 3) == "+91") {
            return preg_replace("/(\+1)(\d{5})(\d{5})/", "$1 $2 $3", $phoneNumber);
        }
        return preg_replace("/(\d{5})(\d{5})/", "$1 $2", $phoneNumber);
    }

    /**
     * @param $string
     * @return string|string[]|null
     */
    public function ucwordsFilter($string) {
        return ucwords($string);
    }

    public function crowdFundingData($id) {
        $eventData = $this->em->getRepository(TrnCircleEvents::class)->findOneBy(array('id' => $id));
        $repository = $this->em->getRepository(TrnOrder::class);
        $query = $repository->createQueryBuilder('o')
            ->select('sum(o.transactionAmount) as totalTransactionAmount, count(o.id) as supporters')
            ->innerJoin('o.trnCircleEvent', 'e')
            ->andWhere('e.id = :event_id OR e.parentTrnCircleEvents = :event_id')
            ->andWhere('e.isActive = 1')
            ->andWhere('o.transactionStatus= :transactionStatus')
            ->setParameter('event_id', $id)
            ->setParameter('transactionStatus', 'success')
            ->getQuery()->getResult();
        $arrCrowdFundingData = array();
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['totalTransactionAmount'])) {
            $arrCrowdFundingData["totalTransactionAmount"] = $query[0]['totalTransactionAmount'];
        } else {
            $arrCrowdFundingData["totalTransactionAmount"] = 0;
        }
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['supporters'])) {
            $arrCrowdFundingData["supporters"] = $query[0]['supporters'];
        } else {
            $arrCrowdFundingData["supporters"] = 0;
        }
        return $arrCrowdFundingData;

    }

    function getIndianCurrency(float $number, bool $isCompleteString = true)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;

            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                if ($isCompleteString) {
                    $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                } else {
                    $str [] = ($number < 21) ? $number . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . ($number % 10) . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                }
            } else {
                $str[] = null;
            }
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
//        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
        return $Rupees.$paise;
    }

    function getGcEmail() {
        return $this->getParameter('gc_email');
    }

    function getWhatsappNumber($withCountryCode = false, $displayOnly = false) {

        $waNo = $this->getParameter('whatsapp_number');
        if($displayOnly == true) {
            if($withCountryCode == true)
                $waNo = '91'.$this->getParameter('whatsapp_number_disp');
            else
                $waNo = $this->getParameter('whatsapp_number_disp');
        }

        return $waNo;
        /*if($withCountryCode == true)
            return '91'.$waNo;
        else
            return $waNo;*/

    }

    function getFundEventCount($objOrgCompany, $circleId) {

        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);

        $crowdfundingEventCount = 0;
        $repository = $this->em->getRepository(TrnCircleEvents::class);
        /* // Not in use for now
        $query = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.orgCompany = :orgCompany_id')
            ->andWhere('e.trnCircle = :circle_id')
            ->andWhere('e.isCrowdFunding = 1')
            ->andWhere('e.isActive = 1')
            ->andWhere('e.mstStatus = :mstStatus1_id')
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->setParameter('circle_id', $circleId)
            ->getQuery()->getResult();

        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $crowdfundingEventCount = $query[0][1];
        }
        */

        $query2 = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.trnFundRaiserCircleEventDetails', 'fre')
            ->where('fre.trnCircleEvents = e.id')
            ->andWhere('e.orgCompany = :orgCompany_id')
            ->andWhere('e.trnCircle = :circle_id')
            ->andWhere('e.mstStatus = :mstStatus1_id')
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->andWhere('e.isActive = 1')
            ->setParameter('circle_id', $circleId)
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->getQuery()->getResult();

        $fundRaiserEventCount = 0;
        if (!empty($query2) && !empty($query2[0]) && !empty($query2[0][1])) {
            $fundRaiserEventCount = $query2[0][1];
        }

        $totalFundEventCount = $crowdfundingEventCount + $fundRaiserEventCount;

        return $totalFundEventCount;
    }

    function getFundEventId($objOrgCompany, $circleId) {

        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);

        $repository = $this->em->getRepository(TrnCircleEvents::class);

        $query = $repository->createQueryBuilder('e')
            ->select('e.id')
            ->innerJoin('e.trnFundRaiserCircleEventDetails', 'fre')
            ->where('fre.trnCircleEvents = e.id')
            ->andWhere('e.orgCompany = :orgCompany_id')
            ->andWhere('e.trnCircle = :circle_id')
            ->andWhere('e.mstStatus = :mstStatus1_id')
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->andWhere('e.isActive = 1')
            ->setParameter('circle_id', $circleId)
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->orderBy('e.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        $id = null;
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['id'])) {
            $id = $query[0]['id'];
        }

        return $id;
    }

    function getUserJoinStatus($objOrgCompany, $userId, $circleId) {

        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);

        $userCnt = 0;
        $repository = $this->em->getRepository(TrnCircleRequestToJoin::class);

        $query2 = $repository->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.orgCompany = :orgCompany_id')
            ->andWhere('r.trnCircle = :circle_id')
            ->andWhere('r.appUser = :user_id')
            ->andWhere('r.mstStatus = :mstStatus1_id')
            ->setParameter('mstStatus1_id', $objMstStatus)
            ->andWhere('r.isActive = 1')
            ->setParameter('circle_id', $circleId)
            ->setParameter('user_id', $userId)
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->getQuery()->getResult();

        if (!empty($query2) && !empty($query2[0]) && !empty($query2[0][1])) {
            $userCnt = $query2[0][1];
        }

        if($userCnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getPendingRequestStatus($objOrgCompany, $userId, $circleId) {

        $userStatus = '';
        $repository = $this->em->getRepository(TrnCircleRequestToJoin::class);

        $query = $repository->createQueryBuilder('r')
            ->andWhere('r.orgCompany = :orgCompany_id')
            ->andWhere('r.trnCircle = :circle_id')
            ->andWhere('r.appUser = :user_id')
            ->andWhere('r.isActive = 1')
            ->setParameter('circle_id', $circleId)
            ->setParameter('user_id', $userId)
            ->setParameter('orgCompany_id', $objOrgCompany)
            ->getQuery()->getResult();

        if (!empty($query)) {
            foreach ($query as $res) {
                $userStatus = $res->getMstStatus()->getStatus();
            }
        }

        return $userStatus;
    }

    /**
     * @param $nEventId
     * @return array
     */
    public function getEventCountData($nEventId, $eventStatus = ''): array
    {
        if($eventStatus != '') {
            $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => $eventStatus]);
        }

        $objOrgCompany = $this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id'));
        $em = $this->em;

        // Member count
        $repository = $em->getRepository(TrnCircleEventRequestToParticipate::class);
        $q = $repository->createQueryBuilder('r')
            ->select('count(r.id)');

        if($nEventId != '') {
            $q->andWhere('r.trnCircleEvent = :event_id')->setParameter('event_id', $nEventId);
        }
        if($eventStatus != '') {
            $q->andWhere('r.mstStatus = :mstStatus_id')->setParameter('mstStatus_id', $objMstStatus);
        }
        $queryMem = $q->getQuery()->getResult();

        if (!empty($queryMem) && !empty($queryMem[0]) && !empty($queryMem[0][1])) {
            $arrCircleCount["memberCount"] = $queryMem[0][1];
        } else {
            $arrCircleCount["memberCount"] = 0;
        }

        // Comment count
        $repository = $em->getRepository(TrnCircleEventComments::class);
        if($nEventId != '') {
            $query = $repository->createQueryBuilder('e')->select('count(e.id)');
        }

        $query->andWhere('e.trnCircleEvents', ':event_id')
//            ->andWhere('e.parentComment is null') // reply will also be counted in comment count
            ->andWhere('e.isApproved = 1')
            ->setParameter('event_id', $nEventId)
            ->getQuery()->getResult();

        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $arrCircleCount["commentCount"] = $query[0][1];
        } else {
            $arrCircleCount["commentCount"] = 0;
        }

        // Bifurcated counts of event types
        /*
        $arrMstEventProductType = $this->em->getRepository(MstEventProductType::class)->findAll();
        $repository = $em->getRepository(TrnCircleEvents::class);
        foreach ($arrMstEventProductType as $objMstEventProductType) {
            $query = $repository->createQueryBuilder('e')
                ->select('count(e.id)')
                ->innerJoin('e.trnCircle', 'c')
                ->innerJoin('e.mstEventProductType', 'p')
                ->where('e.orgCompany = :orgCompany_id')
                ->andWhere('c.id = :circle_id')
                ->andWhere('p.id = :mstEventProductType_id')
                ->andWhere('c.mstStatus = :mstStatus_id')
                ->andWhere('e.mstStatus = :mstStatus1_id')
                ->setParameter('circle_id', $trnCircle)
                ->setParameter('mstStatus_id', $objMstStatus)
                ->setParameter('orgCompany_id', $objOrgCompany)
                ->setParameter('mstEventProductType_id', $objMstEventProductType)
                ->setParameter('mstStatus1_id', $objMstStatus)
                ->getQuery()->getResult();
            $strEventProductType = str_ireplace(array("(", ")", " "), array("", "", ""), strtolower
            ($objMstEventProductType->getEventProductType()));

            if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
                $arrCircleCount[$strEventProductType] = $query[0][1];
            } else {
                $arrCircleCount[$strEventProductType] = 0;
            }
        }*/

        // Total funds raised
        $orderRepository = $this->em->getRepository(TrnOrder::class);
        $sql = $orderRepository->createQueryBuilder('o')
            ->select('sum(o.transactionAmount) as totalTransactionAmount')
            ->innerJoin('o.trnCircleEvent', 'e')
            ->andWhere('e.id = :event_id')
            ->andWhere('e.isActive = 1')
            ->andWhere('o.transactionStatus = :transactionStatus')
            ->setParameter('event_id', $nEventId)
            ->setParameter('transactionStatus', 'success')
            ->getQuery();

        $query = $sql->getResult();
        $totalAmt = 0;
        if (!empty($query) && !empty($query[0]) && !empty($query[0]['totalTransactionAmount'])) {
            $totalAmt = $query[0]['totalTransactionAmount'];
        }
        $lacConvertor = 100000;
        $arrCircleCount['totalFundRaised'] = $totalAmt / $lacConvertor;
        return $arrCircleCount;
    }

    /**
     * @param $nCircleId
     * @param $page
     * @param $projectType
     * @param $projectName
     * @return array
     */
    public function getValidProjectButtons($nCircleId, $page, $projectType, $projectName): array
    {
        /*
        Params required:
        App user to get logged in user
        project type,
        join request status
        fundevent count => if 1 then event id

         * */
//        dd($this->security->getUser());

        $loggedInUser = $this->security->getUser();
        $fundEventCount = $this->getFundEventCount($this->getParameter('company_id'),$nCircleId );
        $isUserJoined = $this->getUserJoinStatus( $this->getParameter('company_id'), $loggedInUser->getId(), $nCircleId);
        $requestStatus = $this->getPendingRequestStatus( $this->getParameter('company_id'), $loggedInUser->getId(), $nCircleId);

        $resultArr = array();
        $buttonArr = array('view' => array(), 'join' => array(), 'donate' => array());
        $resultArr[$page] = $buttonArr;

        if($projectType == 'Close') {
            if($loggedInUser) {
                // if user logged in

                if($page == 'project_list') {

                    $resultArr['project_list'] = [
                        'view' => array('isValid' => true,'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => false, 'label' => 'Request To Join', 'url' => '')
                    ];

                    if($fundEventCount > 0) {
                        if($requestStatus == '') {
                            // not yet requested
                            $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                        } else if($requestStatus == 'Accepted') {
                            // join request is accepted
                            $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => '');
                        } else if($requestStatus == 'Pending Activation') {
                            // join request is accepted
                            $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => 'alert');
                        } else {
                            // join request is rejected or any other
                            $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                        }
                    } else {
                        $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                    }
                } else {
                    $resultArr['project_detail'] = [
                        'view' => array('isValid' => false,'label' => 'View', 'url' => '')
                    ];
                    if($requestStatus == '') {
                        // not yet requested
                        $resultArr['project_detail']['join'] = array('isValid' => true, 'label' => 'Request To Join', 'url' => '');
                        $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                    } else if($requestStatus == 'Accepted') {
                        // join request is accepted
                        $resultArr['project_detail']['join'] = array('isValid' => false, 'label' => 'Request To Join', 'url' => '');
                        if($fundEventCount > 0) {
                            $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => '');
                        }
                    } else if($requestStatus == 'Pending Activation') {
                        // join request is accepted
                        $resultArr['project_detail']['join'] = array('isValid' => false, 'label' => 'Request To Join', 'url' => '');
                        if($fundEventCount > 0) {
                            $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => 'alert');
                        }
                    } else {
                        // join request is rejected or pending for approval
                        $resultArr['project_detail']['join'] = array('isValid' => false, 'label' => 'Request To Join', 'url' => '');
                        $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                    }
                }
            } else {
                // close project and if user not logged in
                if($page == 'project_list') {
                    $resultArr['project_list'] = [
                        'view' => array('isValid' => true, 'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => false, 'label' => 'Request To Join', 'url' => '')
                    ];
                    if($fundEventCount > 0) {
                        $resultArr['project_list']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => 'donatebeforelogin');
                    } else {
                        $resultArr['project_list']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                    }
                } else {
                    // as user is not logged in - join request is empty
                    $resultArr['project_detail'] = [
                        'view' => array('isValid' => false,'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => true, 'label' => 'Request To Join', 'url' => ''),
                        'donate' => array('isValid' => false, 'label' => 'Donate', 'url' => '')
                    ];
                }
            }
        } else {
            // project type is open
            if($loggedInUser) {
                // project is open and if user logged in
                if($page == 'project_list') {
                    $resultArr['project_list'] = [
                        'view' => array('isValid' => true, 'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => false, 'label' => 'Join', 'url' => ''),
//                        'donate' => array('isValid' => true, 'label' => 'Donate', 'url' => '')
                    ];
                    // request type check not required for open project
                    if($fundEventCount > 0) {
                        $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => '');
                    }

                } else {
                    $resultArr['project_detail'] = [
                        'view' => array('isValid' => false,'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => false, 'label' => 'Join', 'url' => ''),
                    ];
                    if($fundEventCount > 0) {
                        $resultArr['project_detail']['donate'] = array('isValid' => true, 'label' => 'Donate', 'url' => '');
                    } else {
                        $resultArr['project_detail']['donate'] = array('isValid' => false, 'label' => 'Donate', 'url' => '');
                    }
                }
            } else {
                // open project and if user not logged in
                if($page == 'project_list') {
                    $resultArr['project_list'] = [
                        'view' => array('isValid' => true, 'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => false, 'label' => 'Join', 'url' => ''),
                        'donate' => array('isValid' => true, 'label' => 'Donate', 'url' => 'donatebeforelogin')
                    ];
                } else {
                    $resultArr['project_detail'] = [
                        'view' => array('isValid' => false,'label' => 'View', 'url' => ''),
                        'join' => array('isValid' => true, 'label' => 'Join', 'url' => ''),
                        'donate' => array('isValid' => false, 'label' => 'Donate', 'url' => 'donatebeforelogin')
                    ];
                }
            }
        }
        return $resultArr;
    }

    /**
     * @param $nEventId
     * @return int
     */
    public function getEventCommentCount($nEventId) : int
    {
        $em = $this->em;

        // Comment count
        $repository = $em->getRepository(TrnCircleEventComments::class);
        $query = $repository->createQueryBuilder('e')
            ->select('count(e.id)')
            ->innerJoin('e.trnCircleEvents', 'ev')
            ->andWhere('ev.id = :event_id')
//            ->andWhere('e.parentComment is null') // reply will also be counted in comment count
            ->andWhere('e.isApproved = 1')
            ->setParameter('event_id', $nEventId)
            ->getQuery()->getResult();

        if (!empty($query) && !empty($query[0]) && !empty($query[0][1])) {
            $commentCount = $query[0][1];
        } else {
            $commentCount = 0;
        }
        return $commentCount;
    }

    /**
     * @param $page
     * @param $eventId
     * @param $circleId
     * @param $eventProductType
     * @param $projectType
     * @param $isTargetAchieved
     * @param $eventStatus
     * @return array
     */
    public function getValidEventButtons($page, $eventId, $circleId, $eventProductType, $projectType, $isTargetAchieved, $eventStatus) {

        $buttonsArray = ['view'=> array('isValid' => false, 'label'=>'View', 'path' => '#' ),
                         'donate' => array('isValid' => false, 'label'=>'Donate', 'path' => '#' ),
                         'join' =>  array('isValid' => false, 'label'=>'Join', 'path' => '#' )
                        ];
        $resultArr = $buttonsArray; // set to default value
        $loggedInUser = $this->security->getUser();

        if($page == 'event_list') {

            $resultArr['view'] = array('isValid' => true, 'label'=>'View', 'path' => array('key'=>'event-details', 'value'=>array('id'=>$eventId)) );

            if(in_array('Fundraiser', $eventProductType)) {

                if($projectType == 'Closed') {
                    if($loggedInUser) {
                        // check status
                        $requestStatus = $this->getPendingRequestStatus( $this->getParameter('company_id'), $loggedInUser->getId(), $circleId);

                        if($requestStatus == '') {
                            // not yet requested
                            $resultArr['donate'] = array('isValid' => true, 'label'=>'Donate', 'path' => array('key'=>'fund-details', 'value'=>array('id'=>$eventId)) );
                        } else if($requestStatus == 'Activated') {
                            // join request is accepted - go to fund detail
                            $resultArr['donate'] = array('isValid' => true, 'label'=>'Donate', 'path' => array('key'=>'fund-details', 'value'=>array('id'=>$eventId)) );
                        } else {
                            // join request is rejected or pending for approval
                            $resultArr['donate'] = array('isValid' => false, 'label'=>'Donate', 'path' => '#');
                        }
                    } else {
                        /*
                            not logged in user
                            login first,
                            check if joined,
                            if joined go to fund detail,
                            else make him join and go to project list with request is under process msg
                        */
                        $resultArr['donate'] = array('isValid' => true, 'label'=>'Donate', 'path' => array('key'=>'event-donate-before-login', 'value'=>array('event'=>$eventId)) );
                    }
                } else {
                    // open project
                    if($loggedInUser) {
                        $resultArr['donate'] = array('isValid' => true, 'label' => 'Donate', 'path' => array('key' => 'fund-details', 'value' => array('id' => $eventId)));
                    } else {
                        $resultArr['donate'] = array('isValid' => true, 'label' => 'Donate', 'path' => array('key' => 'event-donate-before-login', 'value' => array('event' => $eventId)));
                    }
                }
            }
        }
        if($isTargetAchieved == true && $resultArr['donate']['isValid'] == true) {
            $resultArr['donate']['isValid'] = false;
            /*$resultArr['donate']['label'] = 'Target Achieved';
            $resultArr['donate']['path'] = 'javascript:void(0);';*/
        }
        if($eventStatus == 'Expired') {
            $resultArr['donate']['isValid'] = false;
        }
        return $resultArr;
    }

    /**
     * @param $appUserId
     * @return array
     */
    public function getUnreadNotificationData($appUserId) {
        $em = $this->em;
        $appUser = $this->appUserRepository->find($appUserId);
        $objMstNotificationStatus = $this->mstNotificationStatusRepository->findOneBy(["notificationStatus" =>  'Unread']);
        $trnNotificationsArr =  $this->trnNotificationsRepository->findBy(['mstNotificationStatus' =>
            $objMstNotificationStatus, 'appUser' => $appUser],
            ['id' => 'DESC']);
        $returnData = array();
        foreach ($trnNotificationsArr as $trnNotifications ) {
            if(empty($trnNotifications->getDescription()))
                continue;
            $returnData[] = array ( 'notification' => $trnNotifications->getDescription(), 'date' =>
                $trnNotifications->getCreatedOn(), 'trnCircle' => $trnNotifications->getTrnCircle(),
                'trnCircleEvents' => $trnNotifications->getTrnCircleEvents(), 'id' => $trnNotifications->getId()  );
        }
        return $returnData;
    }

    /**
     * @param $appUserId
     * @param $nTrnCircleId
     * @return string|null
     */
    public function getRequestToJoinStatus($appUserId, $nTrnCircleId)
    {
        $trnCircle = $this->trnCircleRepository->find($nTrnCircleId);
        $appUser = $this->appUserRepository->find($appUserId);
        $trnCircleRequestToJoin = $this->trnCircleRequestToJoinRepository->findOneBy(['trnCircle' => $trnCircle, 'appUser' => $appUser]);
        if (!empty($trnCircleRequestToJoin)) {
            return $trnCircleRequestToJoin->getMstStatus()->getStatus();
        }
        return '';
    }

    /**
     * @return bool
     */
    public function checkIfAllMandatoryFilled()
    {
        $appUser = $this->security->getUser();
        if (empty($appUser))
            return true;
        return $this->myAccoutService->checkIfMandatoryFieldsFilled($appUser);
    }

    public function getChangeMakerByUser($user) {

        $mstCategory = $this->em->getRepository(MstArticleCategory::class)->findOneBy(['articleCategory' => 'Change Makers']);

        return $this->em->getRepository(CmsArticle::class)->getChangeMakerByUserId($mstCategory->getId(), $user->getId());
    }

    public function getSecondaryAreaOfInterest($primaryAI) {
        return $this->em->getRepository(MstAreaInterest::class)->getSecondaryAreaOfInterest($primaryAI);
    }

    public function getDaysList() {
        return $this->commonHelper->daysList();
    }

    public function getActiveNewsCount() {
        return $this->em->getRepository(CmsPressRoom::class)->getActiveNewsCount();
    }

}
