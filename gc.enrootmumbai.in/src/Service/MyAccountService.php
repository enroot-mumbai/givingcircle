<?php

namespace App\Service;

use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstPaymentGateway;
use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventDeactivatingReason;
use App\Entity\Transaction\TrnCircleEventBroadCastDetails;
use App\Entity\Transaction\TrnCircleEventInvitations;
use App\Entity\Transaction\TrnCircleEventLeads;
use App\Entity\Transaction\TrnCircleEventReminder;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCircleInvitations;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnAppUserContacts;
use App\Entity\Transaction\TrnCrowdFundEventDistributedDetails;
use App\Entity\Transaction\TrnCrowdFundEventDocuments;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use App\Entity\Transaction\TrnMaterialInKindCircleEventCollectionCentre;
use App\Entity\Transaction\TrnMaterialReceivedAtCollectionCentre;
use App\Entity\Transaction\TrnOrder;
use App\Entity\Transaction\TrnOrderDetail;
use App\Entity\Transaction\TrnVolunterInterest;
use App\Repository\Master\MstEventProductTypeRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnAppUserContactsRepository;
use App\Repository\Transaction\TrnCircleEventBroadCastDetailsRepository;
use App\Repository\Transaction\TrnCircleEventRequestToParticipateRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleInvitationsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnCircleEventLeadsRepository;
use App\Repository\Transaction\TrnCircleRequestToJoinRepository;
use App\Repository\Transaction\TrnCollectionCentreDetailsRepository;
use App\Repository\Transaction\TrnMaterialInKindCircleEventCollectionCentreRepository;
use App\Repository\Transaction\TrnMaterialReceivedAtCollectionCentreRepository;
use App\Repository\Transaction\TrnOrderRepository;
use App\Repository\Transaction\TrnVolunteerCircleParticipationDetailsRepository;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use App\Repository\Transaction\TrnCrowdFundEventOfflineTransferRepository;
use App\Service\PaymentGateway\PayU\PayU;
use Container6EFEppJ\getCache_SecurityExpressionLanguageService;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

class MyAccountService
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SecuritydoProcess
     */
    private $security;
    /**
     * @var CommonHelper
     */
    private $commonHelper;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TrnVolunterInterestRepository
     */
    private $trnVolunterInterestRepository;

    /**
     * @var MstStatusRepository
     */
    private $mstStatusRepository;

    /**
     * @var TrnCircleRepository
     */
    private $trnCircleRepository;

    /**
     * @var TrnCircleRequestToJoinRepository
     */
    private $trnCircleRequestToJoinRepository;

    /**
     * @var TrnOrderRepository
     */
    private $trnOrderRepository;

    /**
     * @var TrnCircleEventsRepository
     */
    private $trnCircleEventsRepository;

    /**
     * @var TrnVolunteerCircleParticipationDetailsRepository
     */
    private $trnVolunteerCircleParticipationDetailsRepository;

    /**
     * @var TrnMaterialReceivedAtCollectionCentreRepository
     */
    private $trnMaterialReceivedAtCollectionCentreRepository;

    /**
     * @var TrnCircleEventRequestToParticipateRepository
     */
    private $trnCircleEventRequestToParticipateRepository;

    /**
     * @var MstEventProductTypeRepository
     */
    private $mstEventProductTypeRepository;

    /**
     * @var TrnCircleEventBroadCastDetailsRepository
     */
    private $trnCircleEventBroadCastDetailsRepository;

    /**
     * @var TrnCircleEventLeadsRepository
     */
    private $trnCircleEventLeadsRepository;

    /**
     * @var AppUserRepository
     */
    private $appUserRepository;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var FileUploaderHelper
     */
    private $fileUploaderHelper;

    /**
     * @var TrnCollectionCentreDetailsRepository
     */
    private $trnCollectionCentreDetailsRepository;

    /**
     * @var ProjectService
     */
    private $projectService;

    /**
     * @var TrnAppUserContactsRepository
     */
    private $trnAppUserContactsRepository;

    /**
     * @var false
     */
    private $error;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var TrnCircleInvitationsRepository
     */
    private $trnCircleInvitationsRepository;

    /**
     * @var TrnMaterialInKindCircleEventCollectionCentreRepository
     */
    private $trnMaterialInKindCircleEventCollectionCentreRepository;

    /**
     * @var TrnCrowdFundEventOfflineTransferRepository
     */
    private $trnCrowdFundEventOfflineTransferRepository;

    /**
     * OrderDetails constructor.
     * @param EntityManagerInterface $em
     * @param CommonHelper $commonHelper
     * @param Security $security
     * @param SessionInterface $session
     * @param TrnVolunterInterestRepository $trnVolunterInterestRepository
     * @param MstStatusRepository $mstStatusRepository
     * @param TrnCircleRepository $trnCircleRepository
     * @param TrnCircleRequestToJoinRepository $trnCircleRequestToJoinRepository
     * @param TrnOrderRepository $trnOrderRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     * @param TrnVolunteerCircleParticipationDetailsRepository $trnVolunteerCircleParticipationDetailsRepository
     * @param TrnMaterialReceivedAtCollectionCentreRepository $trnMaterialReceivedAtCollectionCentreRepository
     * @param TrnCircleEventRequestToParticipateRepository $trnCircleEventRequestToParticipateRepository
     * @param MstEventProductTypeRepository $mstEventProductTypeRepository
     * @param TrnCircleEventBroadCastDetailsRepository $trnCircleEventBroadCastDetailsRepository
     * @param TrnCircleEventLeadsRepository $trnCircleEventLeadsRepository
     * @param AppUserRepository $appUserRepository
     * @param Mailer $mailer
     * @param FileUploaderHelper $fileUploaderHelper
     * @param TrnCollectionCentreDetailsRepository $trnCollectionCentreDetailsRepository
     * @param ProjectService $projectService
     * @param TrnAppUserContactsRepository $trnAppUserContactsRepository
     * @param NotificationService $notificationService
     * @param TrnCircleInvitationsRepository $trnCircleInvitationsRepository
     * @param TrnMaterialInKindCircleEventCollectionCentreRepository $trnMaterialInKindCircleEventCollectionCentreRepository
     */
    public function __construct(EntityManagerInterface $em, CommonHelper $commonHelper, Security $security,
                                SessionInterface $session, TrnVolunterInterestRepository
                                $trnVolunterInterestRepository, MstStatusRepository $mstStatusRepository,
                                TrnCircleRepository $trnCircleRepository, TrnCircleRequestToJoinRepository
                                $trnCircleRequestToJoinRepository, TrnOrderRepository $trnOrderRepository,
                                TrnCircleEventsRepository $trnCircleEventsRepository,
                                TrnVolunteerCircleParticipationDetailsRepository
                                $trnVolunteerCircleParticipationDetailsRepository,
                                TrnMaterialReceivedAtCollectionCentreRepository
                                $trnMaterialReceivedAtCollectionCentreRepository,
                                TrnCircleEventRequestToParticipateRepository $trnCircleEventRequestToParticipateRepository,
                                MstEventProductTypeRepository $mstEventProductTypeRepository,
                                TrnCircleEventBroadCastDetailsRepository $trnCircleEventBroadCastDetailsRepository,
                                TrnCircleEventLeadsRepository $trnCircleEventLeadsRepository, AppUserRepository
                                $appUserRepository, Mailer $mailer, FileUploaderHelper $fileUploaderHelper,
                                TrnCollectionCentreDetailsRepository $trnCollectionCentreDetailsRepository,
                                ProjectService $projectService, TrnAppUserContactsRepository
                                $trnAppUserContactsRepository, NotificationService $notificationService,
                                TrnCircleInvitationsRepository $trnCircleInvitationsRepository, 
                                TrnMaterialInKindCircleEventCollectionCentreRepository $trnMaterialInKindCircleEventCollectionCentreRepository,
                                TrnCrowdFundEventOfflineTransferRepository $trnCrowdFundEventOfflineTransferRepository
    )
    {
        $this->em = $em;
        $this->commonHelper = $commonHelper;
        $this->security = $security;
        $this->session = $session;
        $this->trnVolunterInterestRepository = $trnVolunterInterestRepository;
        $this->mstStatusRepository = $mstStatusRepository;
        $this->trnCircleRepository = $trnCircleRepository;
        $this->trnCircleRequestToJoinRepository = $trnCircleRequestToJoinRepository;
        $this->trnOrderRepository = $trnOrderRepository;
        $this->trnCircleEventsRepository = $trnCircleEventsRepository;
        $this->trnVolunteerCircleParticipationDetailsRepository = $trnVolunteerCircleParticipationDetailsRepository;
        $this->trnMaterialReceivedAtCollectionCentreRepository = $trnMaterialReceivedAtCollectionCentreRepository;
        $this->trnCircleEventRequestToParticipateRepository = $trnCircleEventRequestToParticipateRepository;
        $this->mstEventProductTypeRepository = $mstEventProductTypeRepository;
        $this->trnCircleEventBroadCastDetailsRepository = $trnCircleEventBroadCastDetailsRepository;
        $this->trnCircleEventLeadsRepository = $trnCircleEventLeadsRepository;
        $this->appUserRepository = $appUserRepository;
        $this->mailer = $mailer;
        $this->fileUploaderHelper = $fileUploaderHelper;
        $this->trnCollectionCentreDetailsRepository = $trnCollectionCentreDetailsRepository;
        $this->projectService = $projectService;
        $this->trnAppUserContactsRepository = $trnAppUserContactsRepository;
        $this->error = false;
        $this->notificationService = $notificationService;
        $this->trnCircleInvitationsRepository = $trnCircleInvitationsRepository;
        $this->trnMaterialInKindCircleEventCollectionCentreRepository = $trnMaterialInKindCircleEventCollectionCentreRepository;
        $this->trnCrowdFundEventOfflineTransferRepository = $trnCrowdFundEventOfflineTransferRepository;
    }

    /**
     * @param $appUser
     * @return string
     */
    public function getProfileCompleteness(AppUser $appUser) {
        $appUserInfo = $appUser->getAppUserInfo();
        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        $i = $j = 0;
        if ($strMemberType == 'Organization') {
            $j++;
            if (!empty($appUserInfo->getMstSalutation())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserFirstName())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserLastName())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getAddress1())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getAddress2())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstCity())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstState())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstCountry())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getPincode())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserEmail())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserMobileNumber())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getPancardNumber())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getDateOfBirth())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstSkillSet())){
                $i++;
            }
            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
            if (!empty($objTrnVolunterDetail)){
                $trnVolunterInterestArr =  $this->trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                    $objTrnVolunterDetail, 'isActive' => 1));
                $j++;
                if(!empty($trnVolunterInterestArr)) {
                    $i++;
                }
            } else {
                $j++;
            }
            $trnBankDetailsArr = $appUser->getTrnBankDetails();
            $trnBankDetail = null;
            $j= $j+ 5;
            if(!empty($trnBankDetailsArr) && !empty($trnBankDetailsArr[0])) {
                $trnBankDetail = $trnBankDetailsArr[0];
                if (!empty($trnBankDetail->getBankName())) {
                    $i++;
                }
                if (!empty($trnBankDetail->getAccountHolderName())) {
                    $i++;
                }
                if (!empty($trnBankDetail->getAccountNumber())) {
                    $i++;
                }
                if (!empty($trnBankDetail->getIfscCode())) {
                    $i++;
                }
                if (!empty($trnBankDetail->getMstBankAccountType())) {
                    $i++;
                }
            }
            $TrnOrganizationDetailsArr = $appUser->getTrnOrganizationDetails();
            $trnOrganizationDetails = null;
            $j= $j+ 7;
            if(!empty($TrnOrganizationDetailsArr) && !empty($TrnOrganizationDetailsArr[0])) {
                $trnOrganizationDetails = $TrnOrganizationDetailsArr[0];
                if(!empty($trnOrganizationDetails->getOrganizationName())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getMstTypeOfOrganization())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getRegistrationCertificateTrustDeed())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getIncorporatedOnDate())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getRegistrationDate80G())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getRegistrationNo80G())) {
                    $i++;
                }
                if(!empty($trnOrganizationDetails->getWebsite())) {
                    $i++;
                }
            }
        } else {
            $j++;
            if (!empty($appUserInfo->getMstSalutation())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserFirstName())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserLastName())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getAddress1())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getAddress2())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstCity())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstState())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstCountry())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getPincode())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserEmail())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getUserMobileNumber())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getPancardNumber())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getDateOfBirth())){
                $i++;
            }
            $j++;
            if (!empty($appUserInfo->getMstSkillSet())){
                $i++;
            }

            $objTrnVolunterDetail =  $appUser->getTrnVolunterDetail();
            if (!empty($objTrnVolunterDetail)){
                $j++;
                if (!empty($objTrnVolunterDetail->getMstGender())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getMstMaritalStatus())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getEducationLevel())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getMstEmploymentStatus())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getDistanceWillingToTravel())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getHasDisability())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getIsWillingToHelpInDisaster())){
                    $i++;
                }
                $j++;
                if (!empty($objTrnVolunterDetail->getMstSourceOfInformation())){
                    $i++;
                }
                $trnVolunterInterestArr =  $this->trnVolunterInterestRepository->findBy(array('trnVolunterDetail' =>
                    $objTrnVolunterDetail, 'isActive' => 1));
                $j++;
                if(!empty($trnVolunterInterestArr)) {
                    $i++;
                }
            } else {
                $j = $j + 9;
            }
        }
        $completeNess = ($i * 100) / $j;
        return intval($completeNess);
    }

    /**
     * @return array
     */
    public function getSkillSetCounts() {
        $sql = "SELECT a.mstskillset_id, count(a.appuserinfo_id) as count
                FROM appuserinfo_mstskillset a
                GROUP BY a.mstskillset_id ";
        $stmt_SkillSetCounts = $this->em->getConnection()->prepare($sql);
        $stmt_SkillSetCounts->execute();
        $SkillSetCounts = $stmt_SkillSetCounts->fetchAll(\PDO::FETCH_ASSOC);
        $arrReturnData = array();
        foreach ($SkillSetCounts as $data) {
            $arrReturnData[$data['mstskillset_id']] = $data['count'];
        }
        return $arrReturnData;
    }

    /**
     * @return array
     */
    public function getAreaInterestCount() {
        $query = $this->trnVolunterInterestRepository->createQueryBuilder('e')
            ->select('i.id', 'count(e.id) as count')
            ->leftJoin('e.areaInterestPrimary', 'i')
            ->groupBy('i.id')
            ->getQuery()->getResult();
        $arrReturnData = array();
        foreach ($query as $data) {
            $arrReturnData[$data['id']] = $data['count'];
        }
        return $arrReturnData;
    }

    /**
     * @param $appUser
     * @return array
     */
    public function getSocialProfileData($appUser) {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        ##################################### As Change Maker #############################################
        $myProjects =  $this->trnCircleRepository->createQueryBuilder('c')
            ->where("c.isActive = :isActive")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("c.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->setParameter('mstStatus', $objMstStatus)
            ->getQuery()->getResult();
        $trnContributor = $trnCircleCount = $trnCircleCountPA = 0;
        $myCoCoreProjects = $this->getMyCoCoreProjects();
        if(!empty($myProjects))
            $trnCircleCount = count($myProjects);
        if(!empty($myCoCoreProjects))
            $trnCircleCount += count($myCoCoreProjects);
        $nMyVolunteerEvnCount = $nMyMaterialEvnCount = $nMyFundEvnCount = $nMyCrowdFundEvnCount = $nMyVolunteerCount =
        $nMyProjectHrs =
        $nDonationAmtToMyProject = 0;
        $arrAllCircleEvent =  $arrCircleEvents = $arrVolunteerData = array();
        foreach ($myProjects as $project) {
            foreach ($project->getTrnCircleEvents() as $trnCircleEvent) {
                if (!empty($trnCircleEvent->getIsActive())) {
                    $arrAllCircleEvent[] = $trnCircleEvent;
                    foreach ($trnCircleEvent->getTrnVolunteerCircleParticipationDetails() as $trnVolunteerCircleParticipationDetail) {
                        if ($trnVolunteerCircleParticipationDetail->getMstStatus() == $objMstStatus) {
                            $nMyProjectHrs += $trnVolunteerCircleParticipationDetail->getNumberOfHours();
                        }
                    }
                    foreach ($trnCircleEvent->getMstEventProductType() as $MstEventProductType) {
                        switch (strtolower($MstEventProductType->getEventProductType())) {
                            case 'volunteer (in time)':
                            {
                                $nMyVolunteerEvnCount++;
                                break;
                            }
                            case 'material (in kind)':
                            {
                                $nMyMaterialEvnCount++;
                                break;
                            }
                            case 'fundraiser':
                            {
                                $arrCircleEvents[] = $trnCircleEvent;
                                $nMyFundEvnCount++;
                                break;
                            }
                            case 'crowdfunding':
                            {
                                $nMyCrowdFundEvnCount++;
                                $arrCircleEvents[] = $trnCircleEvent;
                                $nMyFundEvnCount++;
                                break;
                            }
                        }
                    }
                }
            }
        }
        foreach ($myCoCoreProjects as $coproject) {
            $project = $coproject->getTrnCircle();
            foreach ($project->getTrnCircleEvents() as $trnCircleEvent) {
                $arrAllCircleEvent[] = $trnCircleEvent;
                if (($trnCircleEvent->getMstStatus() == 'Activated')) {
                    foreach ($trnCircleEvent->getTrnVolunteerCircleParticipationDetails() as $trnVolunteerCircleParticipationDetail) {
                        if ($trnVolunteerCircleParticipationDetail->getMstStatus() == $objMstStatus) {
                            $nMyProjectHrs += $trnVolunteerCircleParticipationDetail->getNumberOfHours();
                        }
                    }
                    foreach ($trnCircleEvent->getMstEventProductType() as $MstEventProductType) {
                        switch (strtolower($MstEventProductType->getEventProductType())) {
                            case 'volunteer (in time)':
                            {
                                $nMyVolunteerEvnCount++;
                                break;
                            }
                            case 'material (in kind)':
                            {
                                $nMyMaterialEvnCount++;
                                break;
                            }
                            case 'fundraiser':
                            case 'crowdfunding':
                            {
                                $arrCircleEvents[] = $trnCircleEvent;
                                $nMyFundEvnCount++;
                                break;
                            }
                        }
                    }
                }
            }
        }
        $arrDonationDataForMyPrj = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'trnCircleEvent'
        => $arrCircleEvents]);
        foreach ($arrDonationDataForMyPrj as $objDonation) {
            $nDonationAmtToMyProject += $objDonation->getTransactionAmount();
        }
        $arrTrnCircleEventRequestToParticipate = $this->trnCircleEventRequestToParticipateRepository->findBy(array
        ('trnCircleEvent' => $arrAllCircleEvent, 'mstStatus' => $objMstStatus));
        $nMyVolunteerCount = count($arrTrnCircleEventRequestToParticipate);

        //My InActive Projects
        $objMstStatusPA = $this->mstStatusRepository->findOneBy(["status" =>  'Pending Activation']);
        $myProjectsPA =  $this->trnCircleRepository->createQueryBuilder('c')
            ->where("c.isActive = :isActive")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("c.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->setParameter('mstStatus', $objMstStatusPA)
            ->getQuery()->getResult();
        if(!empty($myProjectsPA))
            $trnCircleCountPA = count($myProjectsPA);
        //My InActive Projects


        $myEventCount = array('volunteer' => $nMyVolunteerEvnCount,'material' => $nMyMaterialEvnCount, 'fund' =>
            $nMyFundEvnCount, 'sum' => ($nMyVolunteerEvnCount + $nMyMaterialEvnCount + $nMyFundEvnCount), 'volunteersCount'
        => $nMyVolunteerCount, 'projectHrs' => $nMyProjectHrs, 'donationToMyPrj' => $nDonationAmtToMyProject,
            'nMyCrowdFundEvnCount' => $nMyCrowdFundEvnCount);

        ##################################### As Change Maker #############################################

        ##################################### As Contributors #############################################
        $arrContributorData = $this->trnCircleRequestToJoinRepository->findBy(['isActive' => 1, "mstStatus" =>
            $objMstStatus, 'appUser' => $appUser]);
        if(!empty($arrContributorData))
            $trnContributor = count($arrContributorData);
        $arrParticipateData = $this->trnVolunteerCircleParticipationDetailsRepository->findBy(['isActive' => 1,'appUser' => $appUser]);
        $nMyVolunteeringHrs = 0;
        $arrTrnCircleEventRequestToParticipate = $this->trnCircleEventRequestToParticipateRepository->findBy(array
        ('isActive' => 1,'appUser' => $appUser, 'mstStatus' => $objMstStatus));
        $nAsAVolunteer = count($arrTrnCircleEventRequestToParticipate);
        foreach ($arrParticipateData as $trnVolunteerCircleParticipationDetail) {
            $nMyVolunteeringHrs += $trnVolunteerCircleParticipationDetail->getNumberOfHours();
        }
        $arrDonationData = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'appUser' => $appUser]);
        $nMyDonationAmt = 0 ;
        foreach ($arrDonationData as $objDonation) {
            $nMyDonationAmt += $objDonation->getTransactionAmount();
        }
        $arrMaterialDonated = $this->trnMaterialReceivedAtCollectionCentreRepository->findBy(['isActive' => 1,'appUser'
        => $appUser ]);
        $nMyMaterialEvents = count($arrMaterialDonated);
        $arrVolunteerParticipating = $this->trnCircleEventRequestToParticipateRepository->findBy(['isActive' =>
            1,'appUser' => $appUser, 'mstProductType' => $this->mstEventProductTypeRepository->findOneBy(["isActive" => 1, 'eventProductType' => 'Volunteer (in Time)'])]);
        $nMyConVolunteerEvnCount = count($arrVolunteerParticipating) ;
        $arrMyContribution = array('myDonationAmt' => $nMyDonationAmt, 'nAsAVolunteer' => $nAsAVolunteer,
            'nMyVolunteeringHrs' => $nMyVolunteeringHrs, 'myDonatingEventCount' => count($arrDonationData),
            'nMyMaterialEvents' => $nMyMaterialEvents, 'nMyConVolunteerEvnCount' =>  $nMyConVolunteerEvnCount,
            'sum' => ( count($arrDonationData) + $nMyMaterialEvents + $nMyConVolunteerEvnCount));
        ##################################### As Contributors #############################################

        return array( 'trnCircleCount' => $trnCircleCount, 'trnContributor' => $trnContributor, 'nDonationCount' =>
            count($arrDonationData) ,'arrDonationData' => $arrDonationData, 'myEventCount' => $myEventCount,
            'myContribution' => $arrMyContribution, 'trnCircleCountPA' => $trnCircleCountPA);
    }

    /**
     * @return mixed
     */
    public function getOwnProjects() {

        return  $this->trnCircleRepository->createQueryBuilder('c')
            ->where("c.isActive = :isActive")
            ->andWhere("c.appUser = :appUser")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->orderBy('c.id','DESC')
            ->getQuery()->getResult();
    }

    /**
     * @return mixed
     */
    public function getProjectsIJoined() {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        $arrData =  $this->trnCircleRepository->createQueryBuilder('c')
            ->select('c','r.requestOn')
            ->innerJoin('c.trnCircleRequestToJoins','r')
            ->where("c.isActive = :isActive")
            ->where("r.isActive = :isActive")
            ->andWhere("r.appUser = :appUser")
            ->andWhere("c.mstStatus = :mstStatus")
            ->andWhere("c.appUser != r.appUser")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->setParameter('mstStatus', $objMstStatus)
            ->orderBy('c.id','DESC')
            ->getQuery()->getResult();
        $appUser =  $this->security->getUser();
        foreach ($arrData as $key => $data){
            $arrAllCircleEvent = array();
            foreach ($data[0]->getTrnCircleEvents() as $trnCircleEvent) {
                if (!empty($trnCircleEvent->getIsActive())) {
                    $arrAllCircleEvent[] = $trnCircleEvent;
                }
            }
            $arrData[$key]['eventJoined'] = 0;
            if (!empty($arrAllCircleEvent)) {
                $trnCircleEventRequestToParticipateExistArr = $this->trnCircleEventRequestToParticipateRepository->findBy(array('isActive' => 1,
                    'trnCircleEvent' => $arrAllCircleEvent, 'appUser' => $appUser));
                $arrData[$key]['eventJoined'] = count($trnCircleEventRequestToParticipateExistArr);
            }
        }
        return $arrData;
    }

    /**
     * @return mixed
     */
    public function getMyCoCoreProjects() {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        return  $this->trnCircleEventLeadsRepository->createQueryBuilder('c')
            ->addSelect('tc')
            ->innerJoin('c.trnCircle','tc')
            ->where("c.isActive = :isActive")
            ->andWhere("tc.isActive = :isActive_id")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("tc.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->setParameter('mstStatus', $objMstStatus)
            ->setParameter('isActive_id', 1)
            ->getQuery()->getResult();
    }

    /**
     * @param TrnCircle $trnCircle
     */
    public function deactivateProject(TrnCircle $trnCircle) {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Deactivated']);
        $trnCircle->setMstStatus($objMstStatus);
        $this->em->persist($trnCircle);
        $this->em->flush();

        $this->projectService->changeProjectEventStatus($trnCircle, $objMstStatus);
        $this->notificationService->setAppUser($trnCircle->getAppUser());
        $this->notificationService->setTrnCircle($trnCircle);
        $this->notificationService->doProcess('Project Deactivation Creator');

        $arrProjectMemberList =  $this->getProjectMemberList($trnCircle);
        if(!empty($arrProjectMemberList) && !empty($arrProjectMemberList['arrContributorData'])) {
            foreach ($arrProjectMemberList['arrContributorData'] as $contributor) {
                $this->notificationService->setAppUser($contributor->getAppUser());
                $this->notificationService->setTrnCircle($trnCircle);
                $this->notificationService->doProcess('Project Deactivation Member');
            }
        }
    }

    /**
     * @param TrnCircle $trnCircle
     */
    public function exitProject(TrnCircle $trnCircle) {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        $trnCircleRequestToJoin = $this->trnCircleRequestToJoinRepository->findOneBy(['isActive' => 1, "mstStatus" =>
            $objMstStatus, 'appUser' =>  $this->security->getUser(), 'trnCircle' => $trnCircle]);
        $objMstStatusDeactivate = $this->mstStatusRepository->findOneBy(["status" =>  'Deactivated']);
        $trnCircleRequestToJoin->setMstStatus($objMstStatusDeactivate);
        $trnCircleRequestToJoin->setIsActive(0);
        $this->em->persist($trnCircleRequestToJoin);
        $this->em->flush();
    }

    /**
     * @param TrnCircle $trnCircle
     * @param array $arrParameters
     * @return array
     */
    public function getProjectMemberList(TrnCircle $trnCircle, $arrParameters = array()) {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        if (empty($arrParameters)) {
            $arrContributorData = $this->trnCircleRequestToJoinRepository->findBy(['isActive' => 1,  'trnCircle' =>
                $trnCircle, 'mstStatus' => $objMstStatus]);
            $arrProjectLead = $this->trnCircleEventLeadsRepository->findBy(['trnCircle' => $trnCircle, 'isActive' => 1]);
        } else {
            $arrContributorData = $this->trnCircleRequestToJoinRepository->createQueryBuilder('e')
                ->where('e.isActive =:active')
                ->andWhere('e.trnCircle =:trnCircle')
                ->andWhere('e.mstStatus =:mstStatus')
                ->andWhere('e.firstName like :quicksearch OR e.lastName like :quicksearch OR 
                                        e.emailAddress like :quicksearch OR e.mobileNumber like :quicksearch ')
                ->setParameter('active', 1)
                ->setParameter('mstStatus', $objMstStatus)
                ->setParameter('trnCircle', $trnCircle)
                ->setParameter('quicksearch', "%$arrParameters[quicksearch]%")
                ->getQuery()->getResult();
            $arrProjectLead = $this->trnCircleEventLeadsRepository->createQueryBuilder('e')
                ->innerJoin('e.appUser','a')
                ->innerJoin('a.appUserInfo','i')
                ->where('e.isActive =:active')
                ->andWhere('e.trnCircle =:trnCircle')
                ->andWhere('i.userEmail like :quicksearch OR i.userFirstName like :quicksearch OR i.userLastName like :quicksearch OR 
                            i.userMobileNumber like :quicksearch ')
                ->setParameter('active', 1)
                ->setParameter('trnCircle', $trnCircle)
                ->setParameter('quicksearch', "%$arrParameters[quicksearch]%")
                ->getQuery()->getResult();
        }

        return array('arrContributorData' => $arrContributorData, 'arrProjectLead' => $arrProjectLead);
    }

    /**
     * @param TrnCircle $trnCircle
     * @param array $arrParameters
     * @return array
     */
    public function getProjectRequestToJoinMemberList(TrnCircle $trnCircle, array $arrParameters = array()) {
        $arrAllCircleEvent = array();
        foreach ($trnCircle->getTrnCircleEvents() as $trnCircleEvent) {
            $arrAllCircleEvent[] = $trnCircleEvent;
        }
        $arrMstStatus = $this->mstStatusRepository->findBy(["status" =>  array('Pending Activation', 'Rejected') ]);
        if (empty($arrParameters)) {
            $projectRequestToJoinMemberListData =   $this->trnCircleEventRequestToParticipateRepository->findBy(array ('isActive' => 1, 'mstStatus' =>
                $arrMstStatus, 'trnCircleEvent' => $arrAllCircleEvent));
        } else {
            $projectRequestToJoinMemberListData =
                $this->trnCircleEventRequestToParticipateRepository->findProjectRequestToJoinMemberList($arrAllCircleEvent,
                    $arrParameters, $arrMstStatus);
        }



        $arrMemberFromEvent = $arrRequestToJoinMemberList = array();
        foreach ($projectRequestToJoinMemberListData as $requestData) {
            $strIndex = $requestData->getTrnCircleEvent()->getTrnCircle()->getId().'_'
                .$requestData->getTrnCircleEvent()->getId().'_'.$requestData->getAppUser()->getId();
            $arrRequestToJoinMemberList[$strIndex]['appUser'] = $requestData->getAppUser();
            $arrMemberFromEvent[] = $requestData->getAppUser()->getId();
            $arrRequestToJoinMemberList[$strIndex]['project']['name'] = $requestData->getTrnCircleEvent()->getTrnCircle()->getCircle();
            $arrRequestToJoinMemberList[$strIndex]['event']['name'] = $requestData->getTrnCircleEvent()->getName();
            $arrRequestToJoinMemberList[$strIndex]['eventResource'][$requestData->getMstProductType()
                ->getEventProductType()] = $requestData;
        }
        $arrContributorData = $this->trnCircleRequestToJoinRepository->findBy(['isActive' => 1,  'trnCircle' =>
            $trnCircle, 'mstStatus' => $arrMstStatus]);
        foreach ($arrContributorData as $TrnCircleRequestToJoin) {
            if (in_array( $TrnCircleRequestToJoin->getAppUser()->getId(), $arrMemberFromEvent) === false) {
                $strIndex = $TrnCircleRequestToJoin->getTrnCircle()->getId() . '_'. $TrnCircleRequestToJoin->getAppUser()->getId();
                $arrRequestToJoinMemberList[$strIndex]['appUser'] = $TrnCircleRequestToJoin->getAppUser();
                $arrRequestToJoinMemberList[$strIndex]['project']['name'] = $TrnCircleRequestToJoin->getTrnCircle()->getCircle();
                $arrRequestToJoinMemberList[$strIndex]['event']['name'] = '-';
                $arrRequestToJoinMemberList[$strIndex]['eventResource'][] = $TrnCircleRequestToJoin;
            }
        }
        return $arrRequestToJoinMemberList;
    }

    /**
     * @param TrnCircle $trnCircle
     * @return array
     */
    public function getProjectEventParticipationData(TrnCircle $trnCircle) {
        $trnCircleEvents = array();
        foreach ($trnCircle->getTrnCircleEvents() as $trnCircleEvent) {
            $trnCircleEvents[] = $trnCircleEvent;
        }
        $trnCircleEventRequestToParticipateExistArr = $this->trnCircleEventRequestToParticipateRepository->findBy(array
        ('trnCircleEvent' => $trnCircleEvents));
        $arrAppUserAccepted = $arrAppUser = $arrTempData =  $arrEventMemberData = array();
        foreach ($trnCircleEventRequestToParticipateExistArr as $trnCircleEventRequestToParticipate) {
            if (empty($arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()])){
                $arrAppUser[] = $trnCircleEventRequestToParticipate->getAppUser();
                $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()] = array();
                if ($trnCircleEventRequestToParticipate->getMstStatus() == 'Activated') {
                    $arrAppUserAccepted[] = $trnCircleEventRequestToParticipate->getAppUser();
                }
            }
            $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()][] =
                $trnCircleEventRequestToParticipate;
        }
        $arrEventMemberData['accepted'] = array();
        $arrEventMemberData['pending']  = array();
        $arrMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findBy(["isActive" => true]);
        $objMstStatusActivated = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        $objMstStatusPendActed = $this->mstStatusRepository->findOneBy(["status" =>  'Pending Activation']);


        foreach ($arrMstEventProductTypeObj as $mstEventProductType) {
            $acceptedParticipant = $this->trnCircleEventRequestToParticipateRepository->findBy(array('trnCircleEvent'
            => $trnCircleEvents, 'mstStatus' => $objMstStatusActivated, 'mstProductType' => $mstEventProductType));
            $pendingParticipant = $this->trnCircleEventRequestToParticipateRepository->findBy(array('trnCircleEvent'
            => $trnCircleEvents, 'mstStatus' => $objMstStatusPendActed, 'mstProductType' => $mstEventProductType));
            $arrEventMemberData['accepted'][$mstEventProductType->getEventProductType()] = count($acceptedParticipant);
            $arrEventMemberData['pending'][$mstEventProductType->getEventProductType()]  = count($pendingParticipant);
        }
        $arrDonationDataForMyPrj = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'trnCircleEvent'
        => $trnCircleEvents]);
        $fundraiserMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
            'eventProductType' => 'Fundraiser']);
        $crowdfundingMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
            'eventProductType' => 'Crowdfunding']);
        $anamounsFundDonation = array();
        foreach ($arrDonationDataForMyPrj as $objDonation) {
            if (  $objDonation->getIsAnonymousDonation() == true) {
                $anamounsFundDonation[] = array('firstName' => 'Anonymous', 'lastName' =>'Donation', 'mobileNo' =>
                    '', 'email' => '' );
            } else {
                if ( !empty($objDonation->getAppUser()) && empty($arrTempData[$objDonation->getAppUser()->getId()])){
                    $arrAppUser[] = $objDonation->getAppUser();
                    $arrTempData[$objDonation->getAppUser()->getId()] = array();
                    $dataTemp = array();
                    if (!empty($objDonation->getTrnFundRaiserCircleEventSubEvents())) {
                        $dataTemp['mstProductType'] = $fundraiserMstEventProductTypeObj;
                    } else {
                        $dataTemp['mstProductType'] = $crowdfundingMstEventProductTypeObj;
                    }
                    $dataTemp['mstStatus']      = 'Activated';
                    $arrTempData[$objDonation->getAppUser()->getId()][] = $dataTemp;
                    $arrEventMemberData['accepted']['Fundraiser'] += 1;
                } else {
                    $arrEventMemberData['accepted']['Fundraiser'] += 1;
                    $anamounsFundDonation[] = array('firstName' => $objDonation->getUserFirstName(), 'lastName' =>$objDonation->getUserLastName(), 'email' =>
                        $objDonation->getUserEmail(), 'mobileNo' => '+91 '.$objDonation->getUserMobileNo());
                }
            }
        }
        $arrEventMemberData['participantData']  = $arrTempData;
        $arrEventMemberData['arrAppUser']       = $arrAppUser;
        $arrEventMemberData['arrAppUserAccepted'] = $arrAppUserAccepted;

        $arrEventMemberData['anamounsFundDonation']       = $anamounsFundDonation;
        $arrEventMemberData['totalMemberCount'] = count($trnCircleEventRequestToParticipateExistArr);
        return $arrEventMemberData;
    }

    /**
     * @param TrnCircle $trnCircle
     * @return array
     */
    public function getProjectEventLeadData(TrnCircle $trnCircle) {
        $arrTemp = $this->trnCircleEventLeadsRepository->findBy(['trnCircle' => $trnCircle, 'isActive' => 1]);
        $arrProjectLead = array();
        foreach ($arrTemp as $trnCircleEventLead) {
            $arrProjectLead[$trnCircleEventLead->getAppUser()->getId()] = $trnCircleEventLead->getAppUser()->getId();
        }
        return $arrProjectLead;
    }

    /**
     * @param TrnCircle $trnCircle
     * @return \App\Entity\Transaction\TrnCircleEventBroadCastDetails[]
     */
    public function getProjectBroadCastMessages(TrnCircle $trnCircle) {
        return $this->trnCircleEventBroadCastDetailsRepository->findBy(['trnCircle' => $trnCircle]);
    }

    /**
     * @param TrnCircle $trnCircle
     * @param $strMessage
     * @param String $to
     * @param OrgCompany $orgCompany
     */
    public function makeEntryForProjectBroadCastMessages(TrnCircle $trnCircle, $strMessage, String $to, OrgCompany $orgCompany) {
        $trnCircleEventBroadCastDetails = new TrnCircleEventBroadCastDetails();
        $trnCircleEventBroadCastDetails->setIsActive(1);
        $trnCircleEventBroadCastDetails->setCreatedOn(new \DateTime());
        $trnCircleEventBroadCastDetails->setAppUser($this->security->getUser());
        $trnCircleEventBroadCastDetails->setOrgCompany($orgCompany);
        $trnCircleEventBroadCastDetails->setMessage($strMessage);
        $trnCircleEventBroadCastDetails->setSentTo($to);
        $trnCircleEventBroadCastDetails->setTrnCircleEvent($trnCircle);
        $this->em->persist($trnCircleEventBroadCastDetails);
        $this->em->flush();
    }

    /**
     * @return mixed
     */
    public function getActiveProjects() {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        return  $this->trnCircleRepository->createQueryBuilder('c')
            ->where("c.isActive = :isActive")
            ->andWhere("c.appUser = :appUser")
            ->andWhere("c.mstStatus = :mstStatus")
            ->setParameter('isActive', 1)
            ->setParameter('appUser', $this->security->getUser())
            ->setParameter('mstStatus', $objMstStatus)
            ->getQuery()->getResult();
    }

    /**
     * @param $requestId
     * @param $strStatusName
     * @return array
     */
    public function updateProjectEventParticipation($requestId , $strStatusName ) : array {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Pending Activation']);
        $trnCircleEventRequestToParticipate = $this->trnCircleEventRequestToParticipateRepository->findOneBy
        (['id' => $requestId,'isActive' => 1, 'mstStatus' => $objMstStatus]);
        $arrReturn = array();
        $bSendProjectEvent = false;
        if (!empty($trnCircleEventRequestToParticipate)) {
            $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  $strStatusName]);
            if (!empty($objMstStatus)) {
                $trnCircle = $trnCircleEventRequestToParticipate->getTrnCircleEvent()->getTrnCircle();
                $appUser = $trnCircleEventRequestToParticipate->getAppUser();
                if ($strStatusName == 'Activated') {
                    $trnCircleRequestToJoinRepository = $this->trnCircleRequestToJoinRepository->findOneBy(['isActive' => 1, 'trnCircle' =>
                        $trnCircle, 'appUser' => $appUser]);
                    if (!empty($trnCircleRequestToJoinRepository)) {
                        $objCurrentStatus = $trnCircleRequestToJoinRepository->getMstStatus();
                        $trnCircleRequestToJoinRepository->setMstStatus($objMstStatus);
                        $this->em->persist($trnCircleRequestToJoinRepository);
                        if($objCurrentStatus->getStatus() != $objMstStatus->getStatus())
                            $bSendProjectEvent = true;
                    }
                }
                $trnCircleEventRequestToParticipate->setMstStatus($objMstStatus);
                $trnCircleEventRequestToParticipate->setStatusUpdatedOn(new \DateTime());
                $trnCircleEventRequestToParticipate->setAppUserStatusUpdatedBy($this->security->getUser());
                $this->em->persist($trnCircleEventRequestToParticipate);
                $this->em->flush();
                $arrReturn['Message'] = 'Successfully updated';
                $arrReturn['status'] = 1;
                $arrReturn['trnCircleEventRequestToParticipate'] = $trnCircleEventRequestToParticipate;
                $notificationService = $this->notificationService;
                if ($bSendProjectEvent) {
                    //Project Request to Join Accepted
                    $notificationService->setAppUser($trnCircleRequestToJoinRepository->getAppUser());
                    $notificationService->setRequesterAppUser($trnCircleRequestToJoinRepository->getAppUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Project Request to Join Accepted');
                }
                if ($strStatusName == 'Activated') {
                    $this->makeEntryForCollectionCentre($appUser, $trnCircle, $trnCircleEventRequestToParticipate->getTrnCircleEvent());
                    //Event Request to Participate Accepted Creator
                    $notificationService->setAppUser($trnCircleEventRequestToParticipate->getTrnCircleEvent()->getAppUser());
                    $notificationService->setRequesterAppUser($appUser);
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Event Request to Participate Accepted Creator');

                    //Event Request to Participate Accepted Requestor
                    $notificationService->setAppUser($appUser);
                    $notificationService->setRequesterAppUser($appUser);
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->setTrnCircleEvents($trnCircleEventRequestToParticipate->getTrnCircleEvent());
                    $notificationService->doProcess('Event Request to Participate Accepted Requester');
                    //$this->mailer->sendSuccessfulProjectRequestToJoinAccepted($appUser->getAppUserInfo()->getUserEmail());
                } else {
                    //Event Request to Participate Rejected
                    $notificationService->setAppUser($appUser);
                    $notificationService->setRequesterAppUser($appUser);
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->setTrnCircleEvents($trnCircleEventRequestToParticipate->getTrnCircleEvent());
                    $notificationService->doProcess('Event Request to Participate Rejected');
                    //$this->mailer->sendSuccessfulProjectRequestToJoinRejected($appUser->getAppUserInfo()->getUserEmail());
                }
            } else {
                $arrReturn['Message'] = 'Invalid Status';
                $arrReturn['status'] = 0;
            }
        } else {
            $arrReturn['Message'] = 'Entry not Found';
            $arrReturn['status'] = 0;
        }
        return $arrReturn;
    }

    /**
     * @param $requestId
     * @param $strStatusName
     * @return array
     */
    public function updateProjectParticipation($requestId , $strStatusName ) : array {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Pending Activation']);
        $trnCircleRequestToJoin = $this->trnCircleRequestToJoinRepository->findOneBy(['isActive' => 1,  'id' =>
            $requestId, 'mstStatus' => $objMstStatus]);
        if (!empty($trnCircleRequestToJoin)) {
            $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  $strStatusName]);
            if (!empty($objMstStatus)) {
                $trnCircle = $trnCircleRequestToJoin->getTrnCircle();
                if ($strStatusName == 'Activated') {
                    $appUser = $trnCircleRequestToJoin->getAppUser();
                    $trnCircleRequestToJoin->setMstStatus($objMstStatus);
                    $this->em->persist($trnCircleRequestToJoin);
                    $this->em->flush();
                    $this->makeEntryForCollectionCentre($appUser, $trnCircle);

                    //Project Request to Join Accepted
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($trnCircleRequestToJoin->getAppUser());
                    $notificationService->setRequesterAppUser($trnCircleRequestToJoin->getAppUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Project Request to Join Accepted');

                    //$this->mailer->sendSuccessfulProjectRequestToJoinAccepted($appUser->getAppUserInfo()->getUserEmail());
                } else {
                    $trnCircleRequestToJoin->setMstStatus($objMstStatus);
                    $this->em->persist($trnCircleRequestToJoin);
                    $this->em->flush();
                    
                    //Project Request to Join Rejected
                    $notificationService = $this->notificationService;
                    $notificationService->setAppUser($trnCircleRequestToJoin->getAppUser());
                    $notificationService->setRequesterAppUser($trnCircleRequestToJoin->getAppUser());
                    $notificationService->setTrnCircle($trnCircle);
                    $notificationService->doProcess('Project Request to Join Rejected');
                    //$this->mailer->sendSuccessfulProjectRequestToJoinRejected($appUser->getAppUserInfo()->getUserEmail());
                }
            }
            $arrReturn['Message'] = 'Successfully updated';
            $arrReturn['status'] = 1;
        } else {
            $arrReturn['Message'] = 'Entry not Found';
            $arrReturn['status'] = 0;
        }
        return $arrReturn;
    }

    /**
     * @param Int $projectId
     * @param Int $appUserId
     * @param Int $eventId
     * @param String $action
     */
    public function addRemoveProjectLead(Int $projectId, Int $appUserId,Int $eventId, String $action) :void {
        $trnCircle = null;
        $trnCircleEvents = null;
        $appUser = null;
        if(!empty($projectId))
            $trnCircle = $this->trnCircleRepository->find($projectId);
        if(!empty($eventId))
            $trnCircleEvents = $this->trnCircleEventsRepository->find($eventId);
        if(!empty($appUserId))
            $appUser = $this->appUserRepository->find($appUserId);
        if ($action == 'add') {
            $objTrnCircleEventLeads = new TrnCircleEventLeads();
            $objTrnCircleEventLeads->setAppUser($appUser);
            $objTrnCircleEventLeads->setTrnCircle($trnCircle);
            if(!empty($trnCircleEvents))
                $objTrnCircleEventLeads->setTrnCircleEvents($trnCircleEvents);
            $objTrnCircleEventLeads->setAddByAppUser($this->security->getUser());
            $objTrnCircleEventLeads->setCreatedOn(new \DateTime());
            $objTrnCircleEventLeads->setIsActive(1);
            $this->em->persist($objTrnCircleEventLeads);
            $this->em->flush();
        } else {
            $arrRequest = array();
            $arrRequest['trnCircle'] = $trnCircle;
            $arrRequest['appUser'] = $appUser;
            if(!empty($trnCircleEvents))
                $arrRequest['trnCircleEvents'] = $trnCircleEvents;
            $objTrnCircleEventLeads = $this->trnCircleEventLeadsRepository->findOneBy($arrRequest);
            if(!empty($objTrnCircleEventLeads)) {
                $objTrnCircleEventLeads->setIsActive(0);
                $this->em->persist($objTrnCircleEventLeads);
                $this->em->flush();
            }
        }
    }

    /**
     * @param AppUser $appUser
     * @param $companyId
     * @param array $arrParameters
     * @return array
     */
    public function getOwnEventData(AppUser $appUser, $companyId, $arrParameters = array()) :array {
        $arrMstEventProductTypeObj = $this->mstEventProductTypeRepository->findBy(["isActive" => 1]);
        $arrTemp = array();
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if (strtolower($MstEventProductType) != 'crowdfunding') {
                $arrTemp[] = $MstEventProductType->getId();
            }
        }
        $arrEventList =  $this->trnCircleEventsRepository->getAllEventsOfUser(array(), $companyId, $appUser, $arrTemp, $arrParameters);
        $arrEventUpComingOrOnGoingDetails =  $this->trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrEventList, $this->em);
        $arrReturnData = array();
        foreach ($arrEventList as $trnCircleEvents) {
            if(!empty($arrEventUpComingOrOnGoingDetails) && !empty($arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()])){
                $arrReturnData[$arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()]['eventUpComingOrOnGoing']][$trnCircleEvents->getId()] = $trnCircleEvents;
            }
        }
        return array('eventListing' => $arrReturnData, 'totalEventCount' => count($arrEventList), 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails);
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param array $arrParameters
     * @return array
     */
    public function getEventMemberList(TrnCircleEvents $trnCircleEvents, $arrParameters = array()) : array
    {
        if (empty($arrParameters)) {
            $trnCircleEventRequestToParticipateExistArr = $this->trnCircleEventRequestToParticipateRepository->findBy(array
            ('trnCircleEvent' => $trnCircleEvents));
            $arrAppUserAccepted = $arrAppUser = $arrTempData =  $arrEventMemberData = array();
            foreach ($trnCircleEventRequestToParticipateExistArr as $trnCircleEventRequestToParticipate) {
                if (empty($arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()])){
                    $arrAppUser[] = $trnCircleEventRequestToParticipate->getAppUser();
                    $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()] = array();
                    if ($trnCircleEventRequestToParticipate->getMstStatus() == 'Activated') {
                        $arrAppUserAccepted[] = $trnCircleEventRequestToParticipate->getAppUser();
                    }
                }
                $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()][] =
                    $trnCircleEventRequestToParticipate;
            }
            $arrEventMemberData['accepted'] = array();
            $arrEventMemberData['pending']  = array();
            $arrMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findBy(["isActive" => true]);
            $objMstStatusActivated = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
            $objMstStatusPendActed = $this->mstStatusRepository->findOneBy(["status" =>  'Pending Activation']);
            foreach ($arrMstEventProductTypeObj as $mstEventProductType) {
                $acceptedParticipant = $this->trnCircleEventRequestToParticipateRepository->findBy(array('trnCircleEvent'
                => $trnCircleEvents, 'mstStatus' => $objMstStatusActivated, 'mstProductType' => $mstEventProductType));
                $pendingParticipant = $this->trnCircleEventRequestToParticipateRepository->findBy(array('trnCircleEvent'
                => $trnCircleEvents, 'mstStatus' => $objMstStatusPendActed, 'mstProductType' => $mstEventProductType));
                $arrEventMemberData['accepted'][$mstEventProductType->getEventProductType()] = count($acceptedParticipant);
                $arrEventMemberData['pending'][$mstEventProductType->getEventProductType()]  = count($pendingParticipant);
            }
            $arrDonationDataForMyPrj = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'trnCircleEvent'
            => $trnCircleEvents]);
            $fundraiserMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
                'eventProductType' => 'Fundraiser']);
            $crowdfundingMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
                'eventProductType' => 'Crowdfunding']);
            $anamounsFundDonation = array();
            foreach ($arrDonationDataForMyPrj as $objDonation) {
                if ( !empty($objDonation->getAppUser()) && empty($arrTempData[$objDonation->getAppUser()->getId()])){
                    $arrAppUser[] = $objDonation->getAppUser();
                    $arrTempData[$objDonation->getAppUser()->getId()] = array();
                    $dataTemp = array();
                    if (!empty($objDonation->getTrnFundRaiserCircleEventSubEvents())) {
                        $dataTemp['mstProductType'] = $fundraiserMstEventProductTypeObj;
                    } else {
                        $dataTemp['mstProductType'] = $crowdfundingMstEventProductTypeObj;
                    }
                    $dataTemp['mstStatus']      = 'Activated';
                    $arrTempData[$objDonation->getAppUser()->getId()][] = $dataTemp;
                    $arrEventMemberData['accepted']['Fundraiser'] += 1;
                } else {
                    $arrEventMemberData['accepted']['Fundraiser'] += 1;
                    $anamounsFundDonation[] = array('firstName' => $objDonation->getUserFirstName(), 'lastName' =>$objDonation->getUserLastName(), 'email' =>
                        $objDonation->getUserEmail(), 'mobileNo' => '+91 '.$objDonation->getUserMobileNo());
                }
            }
            $arrEventMemberData['participantData']  = $arrTempData;
            $arrEventMemberData['arrAppUser']       = $arrAppUser;
            $arrEventMemberData['arrAppUserAccepted'] = $arrAppUserAccepted;

            $arrEventMemberData['anamounsFundDonation']       = $anamounsFundDonation;
            $arrEventMemberData['totalMemberCount'] = count($trnCircleEventRequestToParticipateExistArr);
        } else {
            $trnCircleEventRequestToParticipateExistArr = $this->trnCircleEventRequestToParticipateRepository->findByCondition($trnCircleEvents, $arrParameters);
            $arrAppUserAccepted = $arrAppUser = $arrTempData =  $arrEventMemberData = array();
            $arrMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findBy(["isActive" => true]);
            $arrEventMemberData['accepted'] = array();
            $arrEventMemberData['pending']  = array();
            foreach ($arrMstEventProductTypeObj as $mstEventProductType) {
                $arrEventMemberData['accepted'][$mstEventProductType->getEventProductType()] = 0;
                $arrEventMemberData['pending'][$mstEventProductType->getEventProductType()]  = 0;
            }
            foreach ($trnCircleEventRequestToParticipateExistArr as $trnCircleEventRequestToParticipate) {
                if (empty($arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()])){
                    $arrAppUser[] = $trnCircleEventRequestToParticipate->getAppUser();
                    $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()] = array();
                    if ($trnCircleEventRequestToParticipate->getMstStatus() == 'Activated') {
                        $arrAppUserAccepted[] = $trnCircleEventRequestToParticipate->getAppUser();
                    }
                }
                $arrTempData[$trnCircleEventRequestToParticipate->getAppUser()->getId()][] =
                    $trnCircleEventRequestToParticipate;
                if($trnCircleEventRequestToParticipate->getMstStatus()->getStatus() == 'Activated') {
                    $arrEventMemberData['accepted'][$trnCircleEventRequestToParticipate->getMstProductType()
                        ->getEventProductType()] += 1;
                } else if($trnCircleEventRequestToParticipate->getMstStatus()->getStatus() == 'Pending Activation') {
                    $arrEventMemberData['pending'][$trnCircleEventRequestToParticipate->getMstProductType()->getEventProductType()]  += 1;
                }
            }
            $fundraiserMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
                'eventProductType' => 'Fundraiser']);
            $crowdfundingMstEventProductTypeObj      = $this->mstEventProductTypeRepository->findOneBy(["isActive" => true,
                'eventProductType' => 'Crowdfunding']);
            $arrFundProductType = array($fundraiserMstEventProductTypeObj->getId(),
                $crowdfundingMstEventProductTypeObj->getId());
            $anamounsFundDonation = array();
            if (!empty($arrParameters['mstProductType']) && in_array($arrParameters['mstProductType'], $arrFundProductType)) {
                $arrDonationDataForMyPrj = $this->trnOrderRepository->getOrderByDetails('success', $trnCircleEvents, $arrParameters);
                foreach ($arrDonationDataForMyPrj as $objDonation) {
                    if ( !empty($objDonation->getAppUser()) && empty($arrTempData[$objDonation->getAppUser()->getId()])){
                        $arrAppUser[] = $objDonation->getAppUser();
                        $arrTempData[$objDonation->getAppUser()->getId()] = array();
                        $dataTemp = array();
                        if (!empty($objDonation->getTrnFundRaiserCircleEventSubEvents())) {
                            $dataTemp['mstProductType'] = $fundraiserMstEventProductTypeObj;
                        } else {
                            $dataTemp['mstProductType'] = $crowdfundingMstEventProductTypeObj;
                        }
                        $dataTemp['mstStatus']      = 'Activated';
                        $arrTempData[$objDonation->getAppUser()->getId()][] = $dataTemp;
                        $arrEventMemberData['accepted']['Fundraiser'] += 1;
                    } else {
                        $arrEventMemberData['accepted']['Fundraiser'] += 1;
                        $anamounsFundDonation[] = array('firstName' => $objDonation->getUserFirstName(), 'lastName' =>$objDonation->getUserLastName(), 'email' =>
                            $objDonation->getUserEmail(), 'mobileNo' => '+91 '.$objDonation->getUserMobileNo());
                    }
                }
            }
            $arrEventMemberData['participantData']  = $arrTempData;
            $arrEventMemberData['arrAppUser']       = $arrAppUser;
            $arrEventMemberData['arrAppUserAccepted'] = $arrAppUserAccepted;

            $arrEventMemberData['anamounsFundDonation']       = $anamounsFundDonation;
            $arrEventMemberData['totalMemberCount'] = count($trnCircleEventRequestToParticipateExistArr);
        }


        return $arrEventMemberData;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param $deactivateEventReason
     */
    public function deactivateEvent(TrnCircleEvents $trnCircleEvents, $deactivateEventReason) {
        $objMstStatusDeactivate = $this->mstStatusRepository->findOneBy(["status" =>  'Deactivated']);
        $trnCircleEvents->setMstStatus($objMstStatusDeactivate);
        $this->em->persist($trnCircleEvents);
        $objTrnCircleEventDeactivatingReason = new TrnCircleEventDeactivatingReason();
        $objTrnCircleEventDeactivatingReason->setDeactivatedByAppUser($this->security->getUser());
        $objTrnCircleEventDeactivatingReason->setDeactivatedOn(new \DateTime());
        $objTrnCircleEventDeactivatingReason->setDeactivatingReason($deactivateEventReason);
        $objTrnCircleEventDeactivatingReason->setTrnCircleEvents($trnCircleEvents);
        $trnCircleEvents->setReasonToReject($deactivateEventReason);
        $this->em->persist($objTrnCircleEventDeactivatingReason);
        $this->em->persist($trnCircleEvents);
        $this->em->flush();

        $notificationService = $this->notificationService;
        $notificationService->setAppUser($trnCircleEvents->getTrnCircle()->getAppUser());
        $notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
        $notificationService->setTrnCircleEvents($trnCircleEvents);
        $notificationService->doProcess('Event Deactivation Creator From MyAccount');
        $arrEventMemberListData = $this->getEventMemberList($trnCircleEvents);
        if (!empty($arrEventMemberListData) && !empty($arrEventMemberListData['participantData']) ){
            foreach ($arrEventMemberListData['participantData'] as $participant) {
                $notificationService->setAppUser($participant->getAppUser());
                $notificationService->doProcess('Event Deactivation Member From MyAccount');
            }
        }

        $this->mailer->sendSuccessfulEventDeactivation($this->security->getUser()->getAppUserInfo()->getUserEmail(),
            $trnCircleEvents->getName(),$this->security->getUser()->getAppUserInfo()->getUserFirstName()
            .' ' .$this->security->getUser()->getAppUserInfo()->getUserLastName(), $deactivateEventReason);
        $arrChildTrnCircleEvents = $this->trnCircleEventsRepository->findBy(['parentTrnCircleEvents' =>
            $trnCircleEvents]);

        foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
            $childTrnCircleEvent->setMstStatus($trnCircleEvents->getMstStatus());
            $childTrnCircleEvent->setReasonToReject($deactivateEventReason);
            $this->em->persist($childTrnCircleEvent);

            $notificationService->setAppUser($childTrnCircleEvent->getTrnCircle()->getAppUser());
            $notificationService->setTrnCircle($childTrnCircleEvent->getTrnCircle());
            $notificationService->setTrnCircleEvents($childTrnCircleEvent);
            $notificationService->doProcess('Event Deactivation Creator From MyAccount');
            $arrEventMemberListData = $this->getEventMemberList($childTrnCircleEvent);
            if (!empty($arrEventMemberListData) && !empty($arrEventMemberListData['participantData']) ){
                foreach ($arrEventMemberListData['participantData'] as $participant) {
                    $notificationService->setAppUser($participant->getAppUser());
                    $notificationService->doProcess('Event Deactivation Member From MyAccount');
                }
            }
        }
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param $arrAppUsers
     * @param $arrEventUpComingOrOnGoingDetails
     */
    public function sendReminderForEvent(TrnCircleEvents $trnCircleEvents, $arrAppUsers, $arrEventUpComingOrOnGoingDetails) {
        $mailerData = array();
        foreach ($arrAppUsers as $userId) {
            $appUser = $this->appUserRepository->find($userId);
            if (empty($appUser))
                continue;
            $objTrnCircleEventReminder = new TrnCircleEventReminder();
            $objTrnCircleEventReminder->setTrnCircleEvents($trnCircleEvents);
            $objTrnCircleEventReminder->setReminderDate(new \DateTime());
            $objTrnCircleEventReminder->setAppUser($appUser);
            $objTrnCircleEventReminder->setReminderSentByAppUser($this->security->getUser());
            $this->em->persist($objTrnCircleEventReminder);
            $mailerData[] = $appUser;
        }
        $this->em->flush();
        $date = $arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()]['startDate'];
        $time = $arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()]['startTime'];
        foreach ($mailerData as $appUser) {
            $this->mailer->sendEventReminder($appUser->getAppUserInfo()->getUserEmail(), $trnCircleEvents->getName(),
                $appUser->getAppUserInfo()->getUserFirstName() .' ' .$appUser->getAppUserInfo()->getUserLastName(),
                $date, $time);
        }

    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param $arrAppUsers
     * @param string $strBroadCastMessage
     * @param $strSentTo
     * @param $newFilename
     * @param $orgCompany
     */
    public function eventBroadcastUpdate(TrnCircleEvents $trnCircleEvents, $arrAppUsers,
                                         $strBroadCastMessage = "", $strSentTo, $newFilename, $orgCompany) {
        $mailerData = array();
        $objTrnCircleEventBroadCastDetails = new TrnCircleEventBroadCastDetails();
        $objTrnCircleEventBroadCastDetails->setCreatedOn(new \DateTime());
        $objTrnCircleEventBroadCastDetails->setAppUser($this->security->getUser());
        $objTrnCircleEventBroadCastDetails->setIsActive(1);
        $objTrnCircleEventBroadCastDetails->setTrnCircleEvent($trnCircleEvents);
        $objTrnCircleEventBroadCastDetails->setTrnCircle($trnCircleEvents->getTrnCircle());
        $objTrnCircleEventBroadCastDetails->setMessage($strBroadCastMessage);
        $objTrnCircleEventBroadCastDetails->setUploadedFile($newFilename);
        $objTrnCircleEventBroadCastDetails->setOrgCompany($orgCompany);
        $objTrnCircleEventBroadCastDetails->setSentTo($strSentTo);
        $objTrnCircleEventBroadCastDetails->setBoardCastDate(new \DateTime());
        foreach ($arrAppUsers as $userId) {
            $appUser = $this->appUserRepository->find($userId);
            if (empty($appUser))
                continue;
            $mailerData[] = $appUser;
            $objTrnCircleEventBroadCastDetails->addSentToAppUser($appUser);
        }
        $this->em->persist($objTrnCircleEventBroadCastDetails);
        $this->em->flush();
        $newFilename = $this->fileUploaderHelper->getPublicPath($objTrnCircleEventBroadCastDetails->getUploadedFile());
        $changeMakerName = $trnCircleEvents->getTrnCircle()->getAppUser()->getAppUserInfo()->getUserFirstName(). ' '.
            $trnCircleEvents->getTrnCircle()->getAppUser()->getAppUserInfo()->getUserLastName();
        $projectName = $trnCircleEvents->getTrnCircle()->getCircle();
        $eventId = $trnCircleEvents->getId();
        $this->notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
        $this->notificationService->setTrnCircleEvents($trnCircleEvents);
        foreach ($mailerData as $appUser) {
            $this->notificationService->setAppUser($appUser);
            $this->notificationService->doProcess('Event Broadcast');
            $this->mailer->sendEventBroadcastUpdate($appUser->getAppUserInfo()->getUserEmail(),
                $trnCircleEvents->getName(),
                $appUser->getAppUserInfo()->getUserFirstName() .' ' .$appUser->getAppUserInfo()->getUserLastName(),
                $changeMakerName, $projectName, $eventId, $newFilename);
        }
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param array $orderby
     * @return TrnCircleEventBroadCastDetails[]
     */
    public function getEventBroadCastMessages(TrnCircleEvents $trnCircleEvents, $orderby = array()) :array {
        return $this->trnCircleEventBroadCastDetailsRepository->getEventBroadCastMessages($trnCircleEvents->getId(), $orderby);
    }

    /**
     * @param AppUser $appUser
     * @param $companyId
     * @param array $arrParameters
     * @return array
     */
    public function getOwnCrowdfundingEventData(AppUser $appUser, $companyId, array $arrParameters = array()) :array
    {
        $arrMstEventProductTypeObj = $this->mstEventProductTypeRepository->findBy(["isActive" => 1]);
        $arrTemp = array();
        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if (strtolower($MstEventProductType) == 'crowdfunding') {
                $arrTemp[] = $MstEventProductType->getId();
            }
        }
        if (empty($arrParameters)) {
            return  $this->trnCircleEventsRepository->getAllEventsOfUser(array(), $companyId, $appUser, $arrTemp);
        } else {
            return  $this->trnCircleEventsRepository->getAllEventsOfUser(array(), $companyId, $appUser, $arrTemp, $arrParameters);
        }

    }

    /**
     * @param AppUser $appUser
     * @param TrnCircle $trnCircle
     * @param TrnCircleEvents|null $trnCircleEvents
     * @return string[]
     */
    public function makeEntryForCollectionCentre(AppUser $appUser, TrnCircle $trnCircle, TrnCircleEvents
    $trnCircleEvents = null) :array
    {
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        $arrCriteria = array('appUser' => $appUser, 'trnCircle' => $trnCircle);
        if (!empty($trnCircleEvents)){
            $arrCriteria['trnCircleEvent'] =  $trnCircleEvents;
        }
        //Check if the entry already exist in collection centre.
        $trnCollectionCentreDetails = $this->trnCollectionCentreDetailsRepository->findOneBy($arrCriteria);
        if(empty($trnCollectionCentreDetails)) {
            $trnCollectionCentreDetails = new TrnCollectionCentreDetails();
            $trnCollectionCentreDetails->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $trnCollectionCentreDetails->setOrgCompany($appUser->getAppUserInfo()->getOrgCompany());
            $trnCollectionCentreDetails->setCreatedOn(new \DateTime());
            $trnCollectionCentreDetails->setAppUser($appUser);
            $trnCollectionCentreDetails->setIsActive(1);
            $trnCollectionCentreDetails->setTrnCircle($trnCircle);
            if (!empty($trnCircleEvents)) {
                $trnCollectionCentreDetails->setTrnCircleEvent($trnCircleEvents);
            }
            $trnCollectionCentreDetails->setMstStatus($objMstStatus);
            $trnCollectionCentreDetails->setMstCountry($appUser->getAppUserInfo()->getMstCountry());
            $trnCollectionCentreDetails->setMstState($appUser->getAppUserInfo()->getMstState());
            $trnCollectionCentreDetails->setMstCity($appUser->getAppUserInfo()->getMstCity());
            $trnCollectionCentreDetails->setAddress1($appUser->getAppUserInfo()->getAddress1());
            $trnCollectionCentreDetails->setAddress2($appUser->getAppUserInfo()->getAddress2());
            $trnCollectionCentreDetails->setFirstName($appUser->getAppUserInfo()->getUserFirstName());
            $trnCollectionCentreDetails->setLastName($appUser->getAppUserInfo()->getUserLastName());
            $trnCollectionCentreDetails->setPinCode($appUser->getAppUserInfo()->getPincode());
            $this->em->persist($trnCollectionCentreDetails);
            $this->em->flush();
            return array('entry' => 'made');
        }
        return array('entry' => 'not made');
    }

    /**
     * @param AppUser $appUser
     * @param TrnCircle $trnCircle
     * @param TrnCircleEvents|null $trnCircleEvents
     * @return bool
     */
    public function isUserAddressUsedAsCollectionCenter(AppUser $appUser, TrnCircle $trnCircle, TrnCircleEvents
    $trnCircleEvents = null) :bool
    {
        $arrCriteria = array('appUser' => $appUser, 'trnCircle' => $trnCircle);
        if (!empty($trnCircleEvents)){
            $arrCriteria['trnCircleEvent'] =  $trnCircleEvents;
        }
        $trnCollectionCentreDetails = $this->trnCollectionCentreDetailsRepository->findOneBy($arrCriteria);
        if (!empty($trnCollectionCentreDetails)) {
            $trnMaterialInKindCircleEventCollectionCentre = $this->trnMaterialInKindCircleEventCollectionCentreRepository->findOneBy(['trnCollectionCentreDetails' =>
                $trnCollectionCentreDetails]);
            if(!empty($trnMaterialInKindCircleEventCollectionCentre)){
                return true;
            } else
                return false;
        }
        else
            return false;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return array
     */
    public function getDonorList(TrnCircleEvents $trnCircleEvents) :array {
        $arrDonorList = array();
        $arrDonationData = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'trnCircleEvent'
        => $trnCircleEvents]);
        foreach ($arrDonationData as $objTrnOrder) {
            $arrDonorList[$objTrnOrder->getUserEmail()] = array('email' => $objTrnOrder->getUserEmail(), 'firstName'
            => $objTrnOrder->getUserFirstName(), 'lastName' => $objTrnOrder->getUserLastName(), 'appUser' =>
                $objTrnOrder->getAppUser() , 'donationAmount' => $objTrnOrder->getTransactionAmount() );
        }
        return $arrDonorList;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param $arrAppUsers
     * @param string $strBroadCastMessage
     * @param $strSentTo
     * @param $newFilename
     * @param $orgCompany
     */
    public function eventBroadcastUpdateCF(TrnCircleEvents $trnCircleEvents, $arrAppUsers, $strBroadCastMessage = "", $strSentTo, $newFilename, $orgCompany) {
        $mailerData = array();
        $objTrnCircleEventBroadCastDetails = new TrnCircleEventBroadCastDetails();
        $objTrnCircleEventBroadCastDetails->setCreatedOn(new \DateTime());
        $objTrnCircleEventBroadCastDetails->setAppUser($this->security->getUser());
        $objTrnCircleEventBroadCastDetails->setIsActive(1);
        $objTrnCircleEventBroadCastDetails->setTrnCircleEvent($trnCircleEvents);
        $objTrnCircleEventBroadCastDetails->setTrnCircle($trnCircleEvents->getTrnCircle());
        $objTrnCircleEventBroadCastDetails->setMessage($strBroadCastMessage);
        $objTrnCircleEventBroadCastDetails->setUploadedFile($newFilename);
        $objTrnCircleEventBroadCastDetails->setOrgCompany($orgCompany);
        $objTrnCircleEventBroadCastDetails->setSentTo($strSentTo);
        $objTrnCircleEventBroadCastDetails->setBoardCastDate(new \DateTime());
        $this->em->persist($objTrnCircleEventBroadCastDetails);
        $this->em->flush();
        $newFilename = "";
        if (!empty($objTrnCircleEventBroadCastDetails->getUploadedFile()))
            $newFilename = $this->fileUploaderHelper->getPublicPath($objTrnCircleEventBroadCastDetails->getUploadedFile());
        $changeMakerName = $trnCircleEvents->getTrnCircle()->getAppUser()->getAppUserInfo()->getUserFirstName(). ' '.
            $trnCircleEvents->getTrnCircle()->getAppUser()->getAppUserInfo()->getUserLastName();
        $projectName = $trnCircleEvents->getTrnCircle()->getCircle();
        $eventId = $trnCircleEvents->getId();

        $this->notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
        $this->notificationService->setTrnCircleEvents($trnCircleEvents);
        $this->notificationService->setAppUser($trnCircleEvents->getAppUser());
        $this->notificationService->doProcess('Crowdfunding Updates Creator');

        foreach ($arrAppUsers as $emailId => $userData) {
            if (!empty($userData) && !empty($userData['appUser'])) {
                $this->notificationService->setDonationAmount($userData['donationAmount']);
                $this->notificationService->setAppUser($userData['appUser']);
                $this->notificationService->doProcess('Crowdfunding Updates Donors');
            }
            $this->mailer->sendEventBroadcastUpdateCF($emailId, $trnCircleEvents->getName(),
                $userData['firstName'] .' ' .$userData['lastName'],
                $changeMakerName, $projectName, $eventId, $newFilename, $strBroadCastMessage);
        }
    }

    /**
     * @param TrnCircle $trnCircle
     * @param AppUser $appUser
     * @param array $arrParameters
     * @return array
     */
    public function getProjectEventParticipationIndividualData(TrnCircle $trnCircle, AppUser $appUser, $arrParameters = array()) :array
    {
        $arrCircleEvents = $arrCountData = $arrReturnData = $arrEventUpComingOrOnGoingDetails = $arrAllCircleEvent =
        $arrParticipateData = $arrJoinedEventData = $arrDonationData = $arrMaterialDonated = array();
        foreach ($trnCircle->getTrnCircleEvents() as $trnCircleEvent) {
            if (!empty($trnCircleEvent->getIsActive())) {
                $arrAllCircleEvent[] = $trnCircleEvent;
            }
        }
        if (!empty($arrAllCircleEvent)) {
            if (empty($arrParameters)) {
                $arrTemp = $this->trnVolunteerCircleParticipationDetailsRepository->findBy(['isActive' => 1,'appUser' =>
                    $appUser, 'trnCircleEvent' => $arrAllCircleEvent]);
                foreach ($arrTemp as $trnVolunteerCircleParticipationDetails) {
                    $arrCircleEvents[$trnVolunteerCircleParticipationDetails->getTrnCircleEvent()->getId()] = $trnVolunteerCircleParticipationDetails->getTrnCircleEvent();
                    $arrJoinedEventData[$trnVolunteerCircleParticipationDetails->getTrnCircleEvent()->getId()][] =
                        array( 'productType' => 'Volunteer (in Time)', 'data' => $trnVolunteerCircleParticipationDetails);
                }
                $arrTemp = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'appUser' =>
                    $appUser, 'trnCircleEvent' => $arrAllCircleEvent]);
                foreach ($arrTemp as $trnOrder) {
                    $arrCircleEvents[$trnOrder->getTrnCircleEvent()->getId()] = $trnOrder->getTrnCircleEvent();
                    $arrJoinedEventData[$trnOrder->getTrnCircleEvent()->getId()][] = array('productType' => 'Fundraiser', 'data' => $trnOrder);
                }
                $arrTemp= $this->trnMaterialReceivedAtCollectionCentreRepository->findBy(['isActive' => 1,'appUser'
                => $appUser, 'trnCircleEvents' => $arrAllCircleEvent ]);
                foreach ($arrTemp as $trnMaterialReceivedAtCollectionCentre) {
                    $arrCircleEvents[$trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents()->getId()] = $trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents();
                    $arrJoinedEventData[$trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents()->getId()][] = array('productType' => 'Material (in Kind)', 'data' => $trnMaterialReceivedAtCollectionCentre);
                }
            } else {
                $arrTemp = $this->trnVolunteerCircleParticipationDetailsRepository->findParticipationDetails($appUser, $arrAllCircleEvent, $arrParameters);
                foreach ($arrTemp as $trnVolunteerCircleParticipationDetails) {
                    $arrCircleEvents[$trnVolunteerCircleParticipationDetails->getTrnCircleEvent()->getId()] = $trnVolunteerCircleParticipationDetails->getTrnCircleEvent();
                    $arrJoinedEventData[$trnVolunteerCircleParticipationDetails->getTrnCircleEvent()->getId()][] =
                        array('productType' => 'Volunteer (in Time)', 'data' => $trnVolunteerCircleParticipationDetails);
                }
                $arrTemp = $this->trnOrderRepository->getOrderByDetailsMultiEvents('success', $appUser, $arrAllCircleEvent, $arrParameters);
                foreach ($arrTemp as $trnOrder) {
                    $arrCircleEvents[$trnOrder->getTrnCircleEvent()->getId()] = $trnOrder->getTrnCircleEvent();
                    $arrJoinedEventData[$trnOrder->getTrnCircleEvent()->getId()][] = array('productType' => 'Fundraiser', 'data' => $trnOrder);
                }
                $arrTemp = $this->trnMaterialReceivedAtCollectionCentreRepository->findParticipationDetails($appUser, $arrAllCircleEvent, $arrParameters);
                foreach ($arrTemp as $trnMaterialReceivedAtCollectionCentre) {
                    $arrCircleEvents[$trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents()->getId()] = $trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents();
                    $arrJoinedEventData[$trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents()->getId()][] = array('productType' => 'Material (in Kind)', 'data' => $trnMaterialReceivedAtCollectionCentre);
                }
            }
        }
        $arrCountData['ONGOING'] = $arrCountData['UPCOMING'] = $arrCountData['PAST'] = 0;
        $arrEventUpComingOrOnGoingDetails =  $this->trnCircleEventsRepository->getEventUpComingOrOnGoingDetails($arrCircleEvents, $this->em);
        foreach ($arrCircleEvents as $trnCircleEvents) {
            if(!empty($arrEventUpComingOrOnGoingDetails) && !empty($arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()])){
                $arrReturnData[$arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()]['eventUpComingOrOnGoing']][$trnCircleEvents->getId()] = $trnCircleEvents;
                $arrCountData[$arrEventUpComingOrOnGoingDetails[$trnCircleEvents->getId()]['eventUpComingOrOnGoing']]++;
            }
        }
        return array('arrJoinedEventData' => $arrJoinedEventData, 'arrEventUpComingOrOnGoingDetails' => $arrEventUpComingOrOnGoingDetails,
            'arrReturnData' => $arrReturnData, 'countArray' => $arrCountData);
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @param int $appUserId
     * @param float $targetAmount
     */
    public function createDistributeEvent(TrnCircleEvents $trnCircleEvents, int $appUserId, float $targetAmount) :void
    {
        $appUser = $this->appUserRepository->find($appUserId);
        if(empty($appUser))
            return;
        $objTrnCircleEvents = new TrnCircleEvents();
        $objTrnCircleEvents->setIsCrowdFunding(1);
        $objTrnCircleEvents->setMstCity($trnCircleEvents->getMstCity());
        $objTrnCircleEvents->setMstState($trnCircleEvents->getMstState());
        $objTrnCircleEvents->setMstCountry($trnCircleEvents->getMstCountry());
        $objTrnCircleEvents->setMstStatus($trnCircleEvents->getMstStatus());
        $objTrnCircleEvents->setMstJoinBy($trnCircleEvents->getMstJoinBy());
        $objTrnCircleEvents->setTrnCircle($trnCircleEvents->getTrnCircle());
        $objTrnCircleEvents->setIsActive($trnCircleEvents->getIsActive());
        $objTrnCircleEvents->setIsUrgent($trnCircleEvents->getIsUrgent());
        $objTrnCircleEvents->setAppUser($appUser);
        $objTrnCircleEvents->setCreatedOn(new \DateTime());
        $objTrnCircleEvents->setOrgCompany($trnCircleEvents->getOrgCompany());
        $objTrnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
        $objTrnCircleEvents->setFromDate($trnCircleEvents->getFromDate());
        $objTrnCircleEvents->setToDate($trnCircleEvents->getToDate());
        $objTrnCircleEvents->setName($trnCircleEvents->getName());
        $objTrnCircleEvents->setEventPurpose($trnCircleEvents->getEventPurpose());
        $MstEventProductTypeObj = $this->em->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true, 'eventProductType' => 'Crowdfunding']);
        $objTrnCircleEvents->addMstEventProductType($MstEventProductTypeObj);
        $objTrnCircleEvents->setHighlightsOfEvent($trnCircleEvents->getHighlightsOfEvent());
        $objTrnCircleEvents->setParentTrnCircleEvents($trnCircleEvents);

        $profileImag = $trnCircleEvents->getProfileImage();
        $value = str_ireplace('files/','',$profileImag);
        $objTrnCircleEvents->setProfileImage($value);

        $backgroundImagePath = $trnCircleEvents->getBackgroundImagePath();
        $valuebg = str_ireplace('files/','',$backgroundImagePath);
        $objTrnCircleEvents->setBackgroundImagePath($valuebg);

        foreach ($trnCircleEvents->getTrnProductMedia() as $trnProductMedia) {
            $newTrnProductMedia = clone $trnProductMedia;
            $objTrnCircleEvents->addTrnProductMedium($newTrnProductMedia);
        }

        foreach ($trnCircleEvents->getTrnAreaOfInterests() as $trnAreaOfInterests) {
            $newTrnAreaOfInterests = clone $trnAreaOfInterests;
            $objTrnCircleEvents->addTrnAreaOfInterest($newTrnAreaOfInterests);
        }

        $this->em->persist($objTrnCircleEvents);

        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])) {
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
            $newTrnCrowdFundEvents = new TrnCrowdFundEvent();
            $newTrnCrowdFundEvents->setTargetAmount($targetAmount);
            $newTrnCrowdFundEvents->setTrnCircleEvent($objTrnCircleEvents);
            $newTrnCrowdFundEvents->setTrnCircle($trnCircleEvents->getTrnCircle());
            $newTrnCrowdFundEvents->setIsDistributedEvent(0);
            $newTrnCrowdFundEvents->setMinimumContribution($trnCrowdFundEvents->getMinimumContribution());
            $newTrnCrowdFundEvents->setMstTargetAmountCurrency($trnCrowdFundEvents->getMstTargetAmountCurrency());
            $newTrnCrowdFundEvents->setMstMinimumContributionCurrency($trnCrowdFundEvents->getMstMinimumContributionCurrency());
            $objTrnCircleEvents->addTrnCrowdFundEvent($newTrnCrowdFundEvents);
            $this->em->persist($newTrnCrowdFundEvents);

            $arrTrnCrowdFundEventDocuments = $trnCrowdFundEvents->getTrnCrowdFundEventDocuments();
            foreach ($arrTrnCrowdFundEventDocuments as $trnCrowdFundEventDocuments) {
                $newTrnCrowdFundEventDocuments =  new TrnCrowdFundEventDocuments();
                $newTrnCrowdFundEventDocuments->setIsActive(1);
                $newTrnCrowdFundEventDocuments->setDocumentCaption($trnCrowdFundEventDocuments->getDocumentCaption());
                $newTrnCrowdFundEventDocuments->setTrnCrowdFundEvent($newTrnCrowdFundEvents);
                $strDocumentPath = $trnCrowdFundEventDocuments->getUploadedFilePath();
                $value = str_ireplace('files/','',$strDocumentPath);
                $newTrnCrowdFundEventDocuments->setUploadedFilePath($value);
                $this->em->persist($newTrnCrowdFundEventDocuments);
                $newTrnCrowdFundEvents->addTrnCrowdFundEventDocument($newTrnCrowdFundEventDocuments);
            }
        }
        $this->em->flush();
        $this->notificationService->setAppUser($appUser);
        $this->notificationService->setTrnCircle($trnCircleEvents->getTrnCircle());
        $this->notificationService->setTrnCircleEvents($trnCircleEvents);
        $this->notificationService->doProcess('Event Created Creator');
        $this->notificationService->doGCProcess('Event Created GC');
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return array
     */
    public function getDistributedEvents(TrnCircleEvents $trnCircleEvents) :array {
        return $this->trnCircleEventsRepository->findBy(['parentTrnCircleEvents' => $trnCircleEvents]);
    }

    /**
     * @param array $arrTrnCircleEvents
     * @param array $arrParameters
     * @return array
     */
    public function getDistributedEventMultiEvents(array $arrTrnCircleEvents, array $arrParameters = array()) :  array {
        if (empty($arrParameters))
            return $this->trnCircleEventsRepository->findBy(['parentTrnCircleEvents' => $arrTrnCircleEvents]);
        else {
            //TODO
            return $this->trnCircleEventsRepository->findBy(['parentTrnCircleEvents' => $arrTrnCircleEvents]);
        }
    }

    /**
     * @param String $title
     * @param String $firstName
     * @param String $lastName
     * @param int $countryCode
     * @param String $mobileNo
     * @param String $emailId
     */
    public function addCreateContactBook(String $title, String $firstName, String $lastName, int $countryCode, String
    $mobileNo, String  $emailId) :void
    {
        $objTrnAppUserContacts = new TrnAppUserContacts();
        $objTrnAppUserContacts->setAppUser($this->security->getUser());
        $objTrnAppUserContacts->setSalutation($title);
        $objTrnAppUserContacts->setFirstName($firstName);
        $objTrnAppUserContacts->setLastName($lastName);
        $objTrnAppUserContacts->setMobileCountryCode($countryCode);
        $objTrnAppUserContacts->setMobileNumber($mobileNo);
        $objTrnAppUserContacts->setEmail($emailId);
        $objTrnAppUserContacts->setIsActive(1);
        $objTrnAppUserContacts->setEmail($emailId);
        $this->em->persist($objTrnAppUserContacts);
        $this->em->flush();
    }

    /**
     * @param AppUser $appUser
     * @param String $strSearchString
     * @return mixed
     */
    public function getUserContacts(AppUser $appUser, String $strSearchString = '') {
        if (empty($strSearchString))
            return $this->trnAppUserContactsRepository->findBy(['appUser' => $appUser, 'isActive' => 1]);
        else{
            return $this->trnAppUserContactsRepository->createQueryBuilder('e')
                          ->where(" e.firstName like :searchString OR e.lastName like :searchString OR 
                          e.mobileNumber like :searchString OR e.email like :searchString ")
                         ->setParameter('searchString', '%'.$strSearchString.'%')
                         ->getQuery()->getResult();
        }
    }

    /**
     * @param String $firstName
     * @param String $lastName
     * @param int $countryCode
     * @param String $mobileNo
     * @param String $emailId
     * @param TrnAppUserContacts $objTrnAppUserContacts
     */
    public function editCreateContactBook(String $title, String $firstName, String $lastName, int $countryCode, String
    $mobileNo, String  $emailId, TrnAppUserContacts $objTrnAppUserContacts)
    {
        $objTrnAppUserContacts->setSalutation($title);
        $objTrnAppUserContacts->setFirstName($firstName);
        $objTrnAppUserContacts->setLastName($lastName);
        $objTrnAppUserContacts->setMobileCountryCode($countryCode);
        $objTrnAppUserContacts->setMobileNumber($mobileNo);
        $objTrnAppUserContacts->setEmail($emailId);
        $objTrnAppUserContacts->setIsActive(1);
        $objTrnAppUserContacts->setEmail($emailId);
        $this->em->persist($objTrnAppUserContacts);
        $this->em->flush();
    }

    /**
     * @param $contactId
     */
    public function deactivateContact($contactId) :void {
        $objTrnAppUserContacts = $this->trnAppUserContactsRepository->find($contactId);
        $objTrnAppUserContacts->setIsActive(0);
        $this->em->persist($objTrnAppUserContacts);
        $this->em->flush();
    }

    /**
     * @param UploadedFile $UploadedFile
     * @return array
     */
    public function importContact(UploadedFile $UploadedFile) :array {
        $arrReturnResponse = array();
        $raw = file_get_contents($UploadedFile->getPathname());
        $lines = explode(PHP_EOL, $raw);
        if (count($lines) == 1) {
            return array( 'status' => 1, 'Message' => 'No Data found');
        }
        unset($lines[0]);

        foreach ($lines as $key => $line) {
            if (!empty($line) && $line && $line != "") {
                $output_candidate = str_getcsv($line);
                if (empty($output_candidate[0]) || empty($output_candidate[1]) || empty($output_candidate[2]) || empty
                    ($output_candidate[3]) || empty($output_candidate[4]) || empty($output_candidate[5])) {

                    $arrReturnResponse[] = "Empty Data for Line " . ($key + 1);
                    $this->error = true;
                    continue;
                }

                if (!is_numeric($output_candidate[4]) || strlen((string)$output_candidate[4]) != 10) {
                    $arrReturnResponse[] = "Invalid Mobile Number for line " . ($key + 1);
                    $this->error = true;
                    continue;
                }

                if ($this->validate_email($output_candidate[5]) === false) {
                    $arrReturnResponse[] = "Invalid Email address " . ($key + 1);
                    $this->error = true;
                    continue;
                }

                $trnContact = $this->trnAppUserContactsRepository->findOneBy(['email' => $output_candidate[5], 'isActive' => 1,
                    'appUser' => $this->security->getUser()]);
                if (!empty($trnContact)) {
                    $arrReturnResponse[] = "User with email already exist in your contact book for line " . ($key + 1);
                    $this->error = true;
                    continue;
                }

                $this->addCreateContactBook($output_candidate[0], $output_candidate[1], $output_candidate[2],
                    $output_candidate[3], $output_candidate[4], $output_candidate[5]);
            }
        }
        return $arrReturnResponse;
    }

    /**
     * @param $email
     * @return bool
     */
    protected function validate_email($email) {
        return (preg_match("/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/", $email) || !preg_match("/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $email)) ? false : true;
    }

    /**
     * @return false
     */
    public function errorWhileUpload() {
        return $this->error;
    }

    /**
     * @param array $contactIDs
     * @param TrnCircle $trnCircle
     * @param $projectLink
     */
    public function sendInviteToJoin(array $contactIDs, TrnCircle $trnCircle, $projectLink) :void
    {
        $arrTrnAppUserContacts = $this->trnAppUserContactsRepository->findBy([ 'id' => $contactIDs, 'isActive' => 1 ]);
        $changeMakerName = $trnCircle->getAppUser()->getAppUserInfo()->getUserFirstName(). ' '. $trnCircle->getAppUser()->getAppUserInfo()->getUserLastName();
        $projectName = $trnCircle->getCircle();
        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  'Activated']);
        foreach ($arrTrnAppUserContacts as $appUserContact) {
            $objTrnCircleInvitations = new TrnCircleInvitations();
            $objTrnCircleInvitations->setEmailAddress($appUserContact->getEmail());
            $objTrnCircleInvitations->setMobileNumber($appUserContact->getMobileNumber());
            $objTrnCircleInvitations->setMobileCountryCode($appUserContact->getMobileCountryCode());
            $objTrnCircleInvitations->setFirstName($appUserContact->getFirstName());
            $objTrnCircleInvitations->setLastName($appUserContact->getFirstName());
            $objTrnCircleInvitations->setMstStatus($objMstStatus);
            $objTrnCircleInvitations->setCreatedOn(new \DateTime());
            $objTrnCircleInvitations->setIsActive(1);
            $objTrnCircleInvitations->setTrnCircle($trnCircle);
            $objTrnCircleInvitations->setTrnAppUserContacts($appUserContact);
            $objTrnCircleInvitations->setAppUserInvitedBy($this->security->getUser());
            $this->em->persist($objTrnCircleInvitations);
            $this->em->flush();
            $this->mailer->sendInviteToJoin($appUserContact->getEmail(), $projectName,
                $appUserContact->getFirstName(). ' '. $appUserContact->getLastName(), $projectLink, $changeMakerName);
        }
    }

    /**
     * @param AppUser $appUser
     * @param TrnCircleEvents $trnCircleEvents
     * @param int $productId
     * @param int $joinDataId
     */
    public function exitEvent(AppUser  $appUser, TrnCircleEvents $trnCircleEvents, int $productId, int $joinDataId) :void {
        $mstEventProductType = $this->mstEventProductTypeRepository->find($productId);
        switch($mstEventProductType->getEventProductType()){
            case 'Volunteer (in Time)': {
                $objTrnVolunteerCircleParticipationDetails = $this->trnVolunteerCircleParticipationDetailsRepository->find($joinDataId);
                $objTrnVolunteerCircleParticipationDetails->setIsActive(0);
                $this->em->persist($objTrnVolunteerCircleParticipationDetails);
                $this->em->flush();
                break;
            }
            case 'Material (in Kind)': {
                $objTrnMaterialReceivedAtCollectionCentre = $this->trnMaterialReceivedAtCollectionCentreRepository->find($joinDataId);
                $objTrnMaterialReceivedAtCollectionCentre->setIsActive(0);
                $this->em->persist($objTrnMaterialReceivedAtCollectionCentre);
                $this->em->flush();
                $objTrnMaterialReceivedAtCollectionCentre = $this->trnMaterialReceivedAtCollectionCentreRepository->find($joinDataId);
                break;
            }
        }
    }

    /**
     * @param TrnCircle $trnCircle
     * @param $arrAppUsers
     * @param string $strBroadCastMessage
     * @param $strSentTo
     * @param $newFilename
     * @param $orgCompany
     */
    public function projectBroadcastUpdate(TrnCircle $trnCircle, $arrAppUsers, $strBroadCastMessage = "", $strSentTo, $newFilename, $orgCompany) {
        $mailerData = array();
        $objTrnCircleEventBroadCastDetails = new TrnCircleEventBroadCastDetails();
        $objTrnCircleEventBroadCastDetails->setCreatedOn(new \DateTime());
        $objTrnCircleEventBroadCastDetails->setAppUser($this->security->getUser());
        $objTrnCircleEventBroadCastDetails->setIsActive(1);
        $objTrnCircleEventBroadCastDetails->setTrnCircleEvent(null);
        $objTrnCircleEventBroadCastDetails->setTrnCircle($trnCircle);
        $objTrnCircleEventBroadCastDetails->setMessage($strBroadCastMessage);
        $objTrnCircleEventBroadCastDetails->setUploadedFile($newFilename);
        $objTrnCircleEventBroadCastDetails->setOrgCompany($orgCompany);
        $objTrnCircleEventBroadCastDetails->setSentTo($strSentTo);
        $objTrnCircleEventBroadCastDetails->setBoardCastDate(new \DateTime());
        foreach ($arrAppUsers as $appUser) {
            $objTrnCircleEventBroadCastDetails->addSentToAppUser($appUser);
        }
        $this->em->persist($objTrnCircleEventBroadCastDetails);
        $this->em->flush();
        $this->notificationService->setTrnCircle($trnCircle);
        foreach ($arrAppUsers as $appUser) {
            $this->notificationService->setAppUser($appUser);
            $this->notificationService->doProcess('Project Broadcast');
        }
    }

    /**
     * @param array $arrUserContacts
     * @param TrnCircle $trnCircle
     * @return array
     */
    public function checkIfInviteAlreadySent($arrUserContacts = array(), TrnCircle $trnCircle) :array{
        $trnCircleInvitationsArr =  $this->trnCircleInvitationsRepository->findBy(['trnAppUserContacts' =>
            $arrUserContacts, 'trnCircle' =>
        $trnCircle]);
        $arrReturnData = array();
        foreach ($trnCircleInvitationsArr as $trnCircleInvitations) {
            $arrReturnData[$trnCircleInvitations->getTrnAppUserContacts()->getId()] = $trnCircleInvitations;
        }
        return $arrReturnData;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     * @return array
     */
    public function getDonationInformation(TrnCircleEvents $trnCircleEvents) :array
    {
        $arrDistributedEvents =  $this->getDistributedEventMultiEvents([$trnCircleEvents]);
        $arrDistributedEvents[] = $trnCircleEvents;
        foreach ($arrDistributedEvents as $trnCircleEvents) {
            if(empty($trnCircleEvents->getTrnCrowdFundEvents()) && $trnCircleEvents->getTrnCrowdFundEvents()[0]){
                $arrTrnCrowdFundEvents[] = $trnCircleEvents->getTrnCrowdFundEvents()[0];
            }
        }
        $arrOnlinePayment = $this->trnOrderRepository->findBy(['transactionStatus' => 'success', 'trnCircleEvent' =>
        $arrDistributedEvents],['id' => 'DESC']);
        $arrOfflinePayment = $this->trnCrowdFundEventOfflineTransferRepository->findBy(['trnCircleEvent' =>
            $arrDistributedEvents, 'isActive' => 1],['id' => 'DESC']);
        return array('arrOnlinePayment' => $arrOnlinePayment, 'arrOfflinePayment' => $arrOfflinePayment);
    }

    /**
     * @param Int $requestId
     * @param String $strStatusName
     * @return array
     */
    public function updateOfflinePaymentStatus(Int $requestId, String $strStatusName) :array
    {
        try {
            $trnCrowdFundEventOfflineTransfer = $this->trnCrowdFundEventOfflineTransferRepository->find($requestId);
            if (empty($trnCrowdFundEventOfflineTransfer))
                return array('status' =>0, 'Message' => 'Not Offline Transaction found');
            $objMstStatus = $this->mstStatusRepository->findOneBy(["status" =>  $strStatusName]);
            if (!empty($objMstStatus)) {
                $trnCrowdFundEventOfflineTransfer->setMstStatus($objMstStatus);
                $this->em->persist($trnCrowdFundEventOfflineTransfer);
                $this->em->flush();
                $objTrnCircleEvent = $trnCrowdFundEventOfflineTransfer->getTrnCircleEvent();
                if(!empty($objTrnCircleEvent->getParentTrnCircleEvents()))
                    $objParentTrnCircleEvents = $objTrnCircleEvent->getParentTrnCircleEvents();
                else
                    $objParentTrnCircleEvents = $objTrnCircleEvent;
                if($strStatusName == 'Activated') {
                    $trnCrowdFundEventOfflineTransfer->setIsActive(0);
                    $this->em->persist($trnCrowdFundEventOfflineTransfer);
                    $this->em->flush();
                    $this->makeEntryForOfflinePayment($trnCrowdFundEventOfflineTransfer, $objTrnCircleEvent);
                    $finalSum = 0;
                    $targetAmount = $objParentTrnCircleEvents->getTrnCrowdFundEvents()[0]->getTargetAmount();
                    foreach ($objParentTrnCircleEvents->getTrnOrders() as $fundOrders) {
                        $finalSum += $fundOrders->getTotalAmount();
                    }
                    $arrChildTrnCircleEvents = $objParentTrnCircleEvents->getChildTrnCircleEvents();
                    foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
                        foreach ($childTrnCircleEvent->getTrnOrders() as $fundOrders) {
                            $finalSum += $fundOrders->getTotalAmount();
                        }
                    }
                    if ($finalSum >= $targetAmount) {
                        $entityManager = $this->getDoctrine()->getManager();
                        $objTrnCircleEvent->setIsTargetAchieved(1);
                        $objMstStatus = $this->mstStatusRepository->findOneBy(["status" => 'Expired']);
                        $objTrnCircleEvent->setMstStatus($objMstStatus);
                        $entityManager->persist($objTrnCircleEvent);
                        $entityManager->flush();

                        $arrChildTrnCircleEvents = $this->trnCircleEventsRepository->findBy(['parentTrnCircleEvents' =>
                            $objTrnCircleEvent]);
                        foreach ($arrChildTrnCircleEvents as $childTrnCircleEvent) {
                            $childTrnCircleEvent->setMstStatus($objTrnCircleEvent->getMstStatus());
                            $entityManager->persist($childTrnCircleEvent);
                        }
                        $notificationService = $this->notificationService;
                        //Event Fundraiser Creator Target Reached
                        $notificationService->setAppUser($objTrnCircleEvent->getAppUser());
                        $notificationService->setTrnCircle($objTrnCircleEvent->getTrnCircle());
                        $notificationService->setTrnCircleEvents($objTrnCircleEvent);
                        $notificationService->setDonorName($trnCrowdFundEventOfflineTransfer->getFirstName() . ' ' .
                            $trnCrowdFundEventOfflineTransfer->getLastName());
                        $notificationService->setDonationAmount($trnCrowdFundEventOfflineTransfer->getAmountDonated());
                        $notificationService->doProcess('Event Fundraiser Participant Target Reached');
                        //Event Fundraiser Participant Target Reached
                        $donors = $this->getDonorList($objTrnCircleEvent);
                        foreach ($donors as $userData) {
                            if (!empty($userData) && !empty($userData['appUser'])) {
                                $notificationService->setDonationAmount($userData['donationAmount']);
                                $notificationService->setAppUser($userData['appUser']);
                                $notificationService->doProcess('Event Fundraiser Participant Target Reached');
                            }
                        }
                    }
                }
                return array('status' =>1, 'Message' => 'Successfully updated');
            } else {
                return array('status' =>0, 'Message' => 'Incorrect Status');
            }
        } catch (Exception $e) {
            return array('status' =>0, 'Message' => $e->getMessage());
        }
    }

    /**
     * @param TrnCrowdFundEventOfflineTransfer $trnCrowdFundEventOfflineTransfer
     * @param TrnCircleEvents $trnCircleEvent
     * @throws \Exception
     */
    public function makeEntryForOfflinePayment(TrnCrowdFundEventOfflineTransfer $trnCrowdFundEventOfflineTransfer,
                                               TrnCircleEvents $trnCircleEvent):void {
        $cartId = Uuid::uuid4()->toString();
        $trnOrder = new TrnOrder();
        $trnOrderDetail = new TrnOrderDetail();
        $tokenUserId = '';
        $tokenCircleEventId = $trnCircleEvent->getId();
        $tokenTime = $this->commonHelper->tokenTime();
        $trnCrowdFundEvent = $trnCircleEvent->getTrnCrowdFundEvents()[0];
        $tokenTrnCrowdFundEventId = $trnCrowdFundEvent->getId();
        $tokenNo = $tokenTime.''.$tokenCircleEventId.''.$tokenUserId.''.$tokenTrnCrowdFundEventId;
        // Token / Order Generation End
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $dateFormat = $now->format('dmy');
        $userTempOrderNo = ''.$dateFormat.'-'.$tokenUserId.'';
        $trnOrder->setCartId($cartId);
        $trnOrder->setCartTokenId(intval($tokenNo));
        $trnOrder->setOrderNo(intval($tokenNo));
        $trnOrder->setUserFirstName($trnCrowdFundEventOfflineTransfer->getFirstName());
        $trnOrder->setUserLastName($trnCrowdFundEventOfflineTransfer->getLastName());
        $trnOrder->setUserEmail($trnCrowdFundEventOfflineTransfer->getEmail());
        $trnOrder->setUserMobileNo($trnCrowdFundEventOfflineTransfer->getMobileNumber());
        $trnOrder->setUserPanNumber('');
        $trnOrder->setTextNote('Offline Payment');
        if (!empty($trnCrowdFundEventOfflineTransfer->getIsAnonymousDonation()))
            $trnOrder->setIsAnonymousDonation(1);
        else
            $trnOrder->setIsAnonymousDonation(0);
        $trnOrder->setAgreeTerms(1);
        $trnOrder->setUserOrderNo($userTempOrderNo);
        $trnOrder->setTotalAmount($trnCrowdFundEventOfflineTransfer->getAmountDonated() );
        $trnOrder->setTransactionAmount($trnCrowdFundEventOfflineTransfer->getAmountDonated() );
        $trnOrder->setTrnCircleEvent($trnCircleEvent);
        $trnOrder->setTransactionCurrency($trnCrowdFundEvent->getMstTargetAmountCurrency());
        $trnOrder->setTrnCrowdFundEvent($trnCrowdFundEvent);
        $trnOrder->setCartDateTime(new \DateTime($trnCrowdFundEventOfflineTransfer->getCreatedOn()->format('Y-m-d H:i:s'),
            new \DateTimeZone('UTC')));
        $trnOrder->setAppuserIPAddress($_SERVER['REMOTE_ADDR']);
        $trnOrder->setAppuserOSBrowserAgent($_SERVER['HTTP_USER_AGENT']);
        $trnOrder->setTransactionId($trnCrowdFundEventOfflineTransfer->getBankTransactionId());
        $trnOrder->setTransactionStatus('success');
        $trnOrder->setPaymentMode('Offline');
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $monthFormat = $now->format('m');
        $yearFormat = $now->format('y');
        // Set TrnOrderDetail
        $trnOrderDetail->setTrnOrder($trnOrder);
        $trnOrderDetail->setContributionAmount($trnCrowdFundEventOfflineTransfer->getAmountDonated());
        $trnOrderDetail->setTrnCircleEvent($trnCircleEvent);
        $trnOrderDetail->setContributionCurrency($trnCrowdFundEvent->getMstTargetAmountCurrency());
        $trnOrderDetail->setTrnCrowdFundEvent($trnCrowdFundEvent);
        $userOrderNo = 'AHG/O/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnCrowdFundEventOfflineTransfer->getId() . '/' . $trnCrowdFundEvent->getId();
        $userInvoiceNo = 'AHG/I/' . $monthFormat . '' . $yearFormat . '/' . $trnCircleEvent->getId() . '/' . $trnCrowdFundEventOfflineTransfer->getId() . '/' . $trnCrowdFundEvent->getId();;
        $trnOrder->setUserOrderNo($userOrderNo);
        $trnOrder->setUserInvoiceNo($userInvoiceNo);
        $trnOrder->setOrderDateTime($now);
        $this->em->persist($trnOrder);
        $this->em->persist($trnOrderDetail);
        $this->em->flush();
    }

    public function checkIfMandatoryFieldsFilled($appUser): bool
    {
        $appUserInfo = $appUser->getAppUserInfo();

        $strMemberType = $appUserInfo->getMstUserMemberType()->getUserMemberType();
        if (empty($appUserInfo->getMstSalutation()))
            return false;
        if (empty($appUserInfo->getUserFirstName())) {
            return false;
        }
        if (empty($appUserInfo->getUserLastName())) {
            return false;
        }
        if (empty($appUserInfo->getAddress1())) {
            return false;
        }
        if (empty($appUserInfo->getMstCity())) {
            return false;
        }
        if (empty($appUserInfo->getMstState())) {
            return false;
        }
        if (empty($appUserInfo->getMstCountry())) {
            return false;
        }
        if (empty($appUserInfo->getPincode())) {
            return false;
        }
        if (empty($appUserInfo->getUserEmail())) {
            return false;
        }
        if ($strMemberType == 'Organization') {
            if (empty($appUserInfo->getPancardNumber())) {
                return false;
            }
            $trnBankDetailsArr = $appUser->getTrnBankDetails();
            if (empty($trnBankDetailsArr))
                return false;
            if (empty($trnBankDetailsArr[0]))
                return false;
            $trnBankDetail = $trnBankDetailsArr[0];
            if (empty($trnBankDetail->getBankName())) {
                return false;
            }
            if (empty($trnBankDetail->getAccountHolderName())) {
                return false;
            }
            if (empty($trnBankDetail->getAccountNumber())) {
                return false;
            }
            if (empty($trnBankDetail->getIfscCode())) {
                return false;
            }
            if (empty($trnBankDetail->getMstBankAccountType())) {
                return false;
            }
            $TrnOrganizationDetailsArr = $appUser->getTrnOrganizationDetails();
            if (empty($TrnOrganizationDetailsArr))
                return false;
            $trnOrganizationDetails = $TrnOrganizationDetailsArr[0];
            if (empty($trnOrganizationDetails))
                return false;
            if (empty($trnOrganizationDetails->getOrganizationName())) {
                return false;
            }
            if (empty($trnOrganizationDetails->getMstTypeOfOrganization())) {
                return false;
            }
            if (empty($trnOrganizationDetails->getRegistrationCertificateTrustDeed())) {
                return false;
            }
            if (empty($trnOrganizationDetails->getIncorporatedOnDate())) {
                return false;
            }
            /*if (empty($trnOrganizationDetails->getRegistrationDate80G())) {
                return false;
            }
            if (empty($trnOrganizationDetails->getRegistrationNo80G())) {
                return false;
            }*/
            /*if (empty($trnOrganizationDetails->getWebsite())) {
                return false;
            }*/
        }
        return true;
    }

    public function getUserAreaOfInterest($user, $objType = 'both') {

        $trnVolunterInterestArr = array();

        $objTrnVolunterDetail =  $user->getTrnVolunterDetail();
        $trnVolunterInterestArr = array();
        if (!empty($objTrnVolunterDetail)){
            $trnVolunterInterestArr =  $this->em->getRepository(TrnVolunterInterest::class)->findBy(array('trnVolunterDetail' =>
                $objTrnVolunterDetail, 'isActive' => 1));
        }

        $resultArr = array('primary' => [], 'secondary' => []);
        foreach ($trnVolunterInterestArr as $trnAreaOfInterest) {
            $primaryArea = $trnAreaOfInterest->getAreaInterestPrimary()->getAreaInterest();
            $primaryAreaId = $trnAreaOfInterest->getAreaInterestPrimary()->getId();
            $resultArr['primary'][$primaryAreaId] = $trnAreaOfInterest->getAreaInterestPrimary();
            $resultArr['secondary'][$primaryAreaId] = array();
            foreach ($trnAreaOfInterest->getAreaInterestSecondary() as $secondaryArea) {
                $resultArr['secondary'][$primaryAreaId][$secondaryArea->getId()] = $secondaryArea->getAreaInterest();
            }
        }

        if($objType == 'primary') {
            return $resultArr['primary'];
        } else if($objType == 'secondary') {
            return $resultArr['secondary'];
        } else {
            return $resultArr;
        }
    }

}
