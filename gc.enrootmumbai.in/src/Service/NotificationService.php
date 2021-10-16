<?php

namespace App\Service;

use App\Entity\Cms\CmsArticle;
use App\Entity\Organization\OrgCompany;
use App\Entity\Organization\OrgCompanyOffice;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnNotifications;
use App\Repository\Cms\CmsNotificationRepository;
use App\Repository\Master\MstNotificationStatusRepository;
use App\Repository\Master\MstStatusRepository;
use App\Repository\Organization\OrgCompanyOfficeRepository;
use App\Repository\Organization\OrgCompanyRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Repository\Transaction\TrnNotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class NotificationService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CmsNotificationRepository
     */
    private $cmsNotificationRepository;

    /**
     * @var MstStatusRepository
     */
    private $mstStatusRepository;

    /**
     * @var MstNotificationStatusRepository
     */
    private $mstNotificationStatusRepository;

    /**
     * @var TrnNotificationsRepository
     */
    private $trnNotificationsRepository;

    /**
     * @var AppUser
     */
    private $appUser;

    /**
     * @var TrnCircle
     */
    private $trnCircle;

    /**
     * @var TrnCircleEvents
     */
    private $trnCircleEvents;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var OrgCompanyRepository
     */
    private $orgCompanyRepository;

    /**
     * @var OrgCompanyOfficeRepository
     */
    private $orgCompanyOfficeRepository;

    /**
     * @var OrgCompany|null
     */
    private $orgCompany;

    /**
     * @var OrgCompanyOffice|null
     */
    private $objOrgCompanyOffice;

    /**
     * @var int
     */
    private $donationAmount;

    /**
     * @var string
     */
    private $strDonorName;

    /**
     * @var null
     */
    private $requesterAppUser;

    /**
     * @var null
     */
    private $cmsArticle;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var AppUserRepository
     */
    private $appUserRepository;

    private $noReplyEmail;

    /**
     * NotificationService constructor.
     * @param EntityManagerInterface $em
     * @param CmsNotificationRepository $cmsNotificationRepository
     * @param MstStatusRepository $mstStatusRepository
     * @param MstNotificationStatusRepository $mstNotificationStatusRepository
     * @param TrnNotificationsRepository $trnNotificationsRepository
     * @param UrlGeneratorInterface $router
     * @param ContainerInterface $container
     * @param OrgCompanyRepository $orgCompanyRepository
     * @param OrgCompanyOfficeRepository $orgCompanyOfficeRepository
     * @param MailerInterface $mailer
     * @param AppUserRepository $appUserRepository
     */
    public function __construct(EntityManagerInterface $em, CmsNotificationRepository $cmsNotificationRepository,
                                MstStatusRepository $mstStatusRepository, MstNotificationStatusRepository $mstNotificationStatusRepository, TrnNotificationsRepository $trnNotificationsRepository, UrlGeneratorInterface $router, ContainerInterface $container, OrgCompanyRepository $orgCompanyRepository,
                                OrgCompanyOfficeRepository $orgCompanyOfficeRepository, MailerInterface $mailer,
                                AppUserRepository $appUserRepository)
    {
        $this->em = $em;
        $this->cmsNotificationRepository = $cmsNotificationRepository;
        $this->mstStatusRepository = $mstStatusRepository;
        $this->mstNotificationStatusRepository = $mstNotificationStatusRepository;
        $this->trnNotificationsRepository = $trnNotificationsRepository;
        $this->router = $router;
        $this->container = $container;
        $this->orgCompanyRepository = $orgCompanyRepository;
        $this->orgCompanyOfficeRepository = $orgCompanyOfficeRepository;
        $this->appUser = $this->trnCircle = $this->trnCircleEvents = $this->requesterAppUser = null;
        $this->orgCompany = $this->orgCompanyRepository->find($this->container->getParameter('company_id'));
        $this->objOrgCompanyOffice = $this->orgCompanyOfficeRepository->findOneBy(['orgCompany' => $this->orgCompany, 'mstOfficeCategory' => 1]);
        $this->donationAmount = 0;
        $this->strDonorName = '';
        $this->cmsArticle = null;
        $this->mailer = $mailer;
        $this->appUserRepository = $appUserRepository;
        $this->noReplyEmail = 'givingcircle@givingcircle.in';
    }

    /**
     * @param TrnNotifications $objTrnNotifications
     */
    public function sendEmail(TrnNotifications $objTrnNotifications)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($this->appUser->getAppUserInfo()->getUserEmail())
            ->subject($objTrnNotifications->getEmailSubject())
            ->html($objTrnNotifications->getEmailContent());
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param TrnNotifications $objTrnNotifications
     * @param $strEmailAddress
     */
    public function sendEmailNoLoggedInUser(TrnNotifications $objTrnNotifications, $strEmailAddress)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($strEmailAddress)
            ->subject($objTrnNotifications->getEmailSubject())
            ->html($objTrnNotifications->getEmailContent());
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendSMS()
    {

    }

    public function sendWhatsApp()
    {

    }

    public function pushNotification()
    {

    }


    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceUsername($strToReplace = "", &$strData)
    {
        $strFirstName = $this->appUser->getAppUserInfo()->getUserFirstName();
        $strLastName = $this->appUser->getAppUserInfo()->getUserLastName();
        $strReplacementString = $strFirstName . ' ' . $strLastName;
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceChangeMakerName($strToReplace = "", &$strData)
    {
        $strFirstName = $this->trnCircle->getAppUser()->getAppUserInfo()->getUserFirstName();
        $strLastName = $this->trnCircle->getAppUser()->getAppUserInfo()->getUserLastName();
        $strReplacementString = $strFirstName . ' ' . $strLastName;
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectName($strToReplace = "", &$strData)
    {
        $strProjectName = $this->trnCircle->getCircle();
        $strReplacementString = $strProjectName;
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventName($strToReplace = "", &$strData)
    {
        $strEventName = $this->trnCircleEvents->getName();
        if (!empty($this->trnCircleEvents->getParentTrnCircleEvents())) {
            $strReplacementString = "<span style='color:#f00;'>$strEventName</span>";
        } else {
            $strReplacementString = $strEventName;
        }
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventLink($strToReplace = "", &$strData)
    {
        $strEventName = $this->trnCircleEvents->getName();
        $strEventLink = $this->router->generate('event-details', ['id' => $this->trnCircleEvents->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>$strEventName</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceMyAccountLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('personal-info', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>My Account</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceMyAccountFundRaiserParticipationReportLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('personal-info', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>My Account</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceMyAccountParticipationReportLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('personal-info', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>My Account</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceMyAccountSocialProfile($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('social-profile', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>My Account</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceMyaccountRequestToJoinLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('view-project-request-to-join', ['id' => $this->trnCircle->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>My Account</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceRequestorName($strToReplace = "", &$strData)
    {
        $strFirstName = $this->requesterAppUser->getAppUserInfo()->getUserFirstName();
        $strLastName = $this->requesterAppUser->getAppUserInfo()->getUserLastName();
        $strReplacementString = $strFirstName . ' ' . $strLastName;
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceGcWhatappNumber($strToReplace = "", &$strData)
    {
        $strMobile = $this->objOrgCompanyOffice->getOfficeTelNumber();
        $strData = str_ireplace($strToReplace, $strMobile, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceWhatappNumber($strToReplace = "", &$strData)
    {
        $strMobile = $this->objOrgCompanyOffice->getOfficeTelNumber();
        $strData = str_ireplace($strToReplace, $strMobile, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceGcEmailId($strToReplace = "", &$strData)
    {
        $strEmail = $this->objOrgCompanyOffice->getOfficeEmail();
        $strData = str_ireplace($strToReplace, $strEmail, $strData);
    }
    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceGcEmail($strToReplace = "", &$strData)
    {
        $strEmail = $this->objOrgCompanyOffice->getOfficeEmail();
        $strData = str_ireplace($strToReplace, $strEmail, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventCreator($strToReplace = "", &$strData)
    {
        $strFirstName = $this->trnCircleEvents->getAppUser()->getAppUserInfo()->getUserFirstName();
        $strLastName = $this->trnCircleEvents->getAppUser()->getAppUserInfo()->getUserLastName();
        $strEventCreator = $strFirstName . ' ' . $strLastName;
        $strData = str_ireplace($strToReplace, $strEventCreator, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceSocialMediaWidget($strToReplace = "", &$strData)
    {
        $strEmail = '';
        $strData = str_ireplace($strToReplace, $strEmail, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceRejectionReason($strToReplace = "", &$strData)
    {
        $strReasonToReject = $this->trnCircle->getReasonToReject();
        $strData = str_ireplace($strToReplace, $strReasonToReject, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventRejectionReason($strToReplace = "", &$strData)
    {
        $strReasonToReject = $this->trnCircleEvents->getReasonToReject();
        $strData = str_ireplace($strToReplace, $strReasonToReject, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectDeactivationReason($strToReplace = "", &$strData)
    {
        $strReasonToReject = $this->trnCircle->getReasonToReject();
        $strData = str_ireplace($strToReplace, $strReasonToReject, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventDeactivationReason($strToReplace = "", &$strData)
    {
        $strReasonToReject = $this->trnCircleEvents->getReasonToReject();
        $strData = str_ireplace($strToReplace, $strReasonToReject, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventProductType($strToReplace = "", &$strData)
    {
        $arrEventPrdType = array();
        foreach ($this->trnCircleEvents->getMstEventProductType() as $mstEventProductType) {
            $arrEventPrdType[] = $mstEventProductType->getEventProductType();
        }
        $strMstEventProductType = implode(', ', $arrEventPrdType);
        $strData = str_ireplace($strToReplace, $strMstEventProductType, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceDonationAmount($strToReplace = "", &$strData)
    {
        $strData = str_ireplace($strToReplace, $this->donationAmount, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceDonorName($strToReplace = "", &$strData)
    {
        $strData = str_ireplace($strToReplace, $this->strDonorName, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectLink($strToReplace = "", &$strData)
    {
        $strEventName = $this->trnCircle->getCircle();
        $strEventLink = $this->router->generate('project-details', ['id' => $this->trnCircle->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>$strEventName</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectCity($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircle->getMstCity()->getCity();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectCause($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircle->getCircleInformation();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceExploreProjectsLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('change-makers-project-list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>Explore Project Links</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectPrimaryAreaOfInterest($strToReplace = "", &$strData)
    {
        $arrTrnAreaOfInterests = $this->trnCircle->getTrnAreaOfInterests();
        $arrPrimaryAI = $arrPrimaryAISecAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $arrPrimaryAI[] = $TrnAreaOfInterest->getAreaInterestPrimary()->getAreaInterest();
        }
        $strReplacementString = implode(', ', $arrPrimaryAI);
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceProjectSecondaryAreaOfInterest($strToReplace = "", &$strData)
    {
        $arrTrnAreaOfInterests = $this->trnCircle->getTrnAreaOfInterests();
        $arrPrimaryAISecAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[] = $areaInterest->getAreaInterest();
            }
        }
        $strReplacementString = implode(', ', $arrPrimaryAISecAI);
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventCreatorName($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircleEvents->getAppUser()->getAppUserInfo()->getUserFirstName() . ' '
            . $this->trnCircleEvents->getAppUser()->getAppUserInfo()->getUserLastName();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventCity($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircleEvents->getMstCity()->getCity();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventCause($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircleEvents->getEventPurpose();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventPrimaryAreaOfInterest($strToReplace = "", &$strData)
    {
        $arrTrnAreaOfInterests = $this->trnCircleEvents->getTrnAreaOfInterests();
        $arrPrimaryAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $arrPrimaryAI[] = $TrnAreaOfInterest->getAreaInterestPrimary();
        }
        $strReplacementString = implode(', ', $arrPrimaryAI);
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceEventSecondaryAreaOfInterest($strToReplace = "", &$strData)
    {
        $arrTrnAreaOfInterests = $this->trnCircleEvents->getTrnAreaOfInterests();
        $arrPrimaryAISecAI = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrPrimaryAISecAI[] = $areaInterest->getAreaInterest();
            }
        }
        $strReplacementString = implode(', ', $arrPrimaryAISecAI);
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceContactUsLink($strToReplace = "", &$strData)
    {
        $strEventLink = $this->router->generate('contact-us', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $strReplacementString = "<a href='$strEventLink' target='_blank'>Contact Us</a>";
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceVistorName($strToReplace = "", &$strData)
    {
        $strFirstName = $this->requesterAppUser->getAppUserInfo()->getUserFirstName();
        $strLastName = $this->requesterAppUser->getAppUserInfo()->getUserLastName();
        $strReplacementString = $strFirstName . ' ' . $strLastName;
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /***
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceBlogTitleName($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->trnCircle->getCircle();
        if (!empty($this->trnCircleEvents)) {
            $strReplacementString = $this->trnCircleEvents->getName();
        }
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param string $strToReplace
     * @param $strData
     */
    private function replaceChangeMakerTitle($strToReplace = "", &$strData)
    {
        $strReplacementString = $this->cmsArticle->getArticleTitle();
        $strData = str_ireplace($strToReplace, $strReplacementString, $strData);
    }

    /**
     * @param $strData
     * @return mixed
     */
    private function replaceDynamics($strData)
    {
        $arrMatchData = $this->find_between($strData, '{', '}');
        if (!empty($arrMatchData) && !empty($arrMatchData[2])) {
            foreach ($arrMatchData[2] as $key => $strFunction) {
                $strFunction = str_ireplace('_', ' ', $strFunction);
                $strFunction = ucwords($strFunction);
                $strFunction = str_ireplace(' ', '', $strFunction);
                $strFunction = 'replace' . $strFunction;
                $this->$strFunction($arrMatchData[0][$key], $strData);
            }
        }
        return $strData;
    }

    /**
     * @param string $string
     * @param string $start
     * @param string $end
     * @param bool $greedy
     * @return mixed
     */
    private function find_between(string $string, string $start, string $end, bool $greedy = false)
    {
        $start = preg_quote($start, '/');
        $end = preg_quote($end, '/');

        $format = '/(%s)(.*';
        if (!$greedy) $format .= '?';
        $format .= ')(%s)/';

        $pattern = sprintf($format, $start, $end);
        preg_match_all($pattern, $string, $matches);
        return $matches;
    }

    /**
     * @param $strNotificationName
     */
    public function doProcess($strNotificationName): void
    {
        $appUser = $this->appUser;
        $cmsNotification = $this->cmsNotificationRepository->findOneBy(['notificationName' => $strNotificationName, 'isActive' => 1]);
        $strPushNotification = $strTextMessage = $strWhatAppMsg = $strEmailContent = $strEmailSubject =
        $strSystemNotification = "";
        if (!empty($cmsNotification)) {
            if (!empty($cmsNotification->getSystemNotification())) {
                $strSystemNotification = $cmsNotification->getSystemNotification();
                $strSystemNotification = $this->replaceDynamics($strSystemNotification);
            }
            if (!empty($cmsNotification->getEmailSubject())) {
                $strEmailSubject = $cmsNotification->getEmailSubject();
                $strEmailSubject = $this->replaceDynamics($strEmailSubject);
                $strEmailSubject = strip_tags($strEmailSubject);
            }
            if (!empty($cmsNotification->getEmail())) {
                $strEmailContent = $cmsNotification->getEmail();
                $strEmailContent = $this->replaceDynamics($strEmailContent);
            }
            if (!empty($cmsNotification->getWhatappMsg())) {
                $strWhatAppMsg = $cmsNotification->getWhatappMsg();
                $strWhatAppMsg = $this->replaceDynamics($strWhatAppMsg);
            }
            if (!empty($cmsNotification->getTextMessage())) {
                $strTextMessage = $cmsNotification->getTextMessage();
                $strTextMessage = $this->replaceDynamics($strTextMessage);
            }
            if (!empty($cmsNotification->getPushNotification())) {
                $strPushNotification = $cmsNotification->getPushNotification();
                $strPushNotification = $this->replaceDynamics($strPushNotification);
            }
            $objMstNotificationStatus = $this->mstNotificationStatusRepository->findOneBy(["notificationStatus" => 'Unread']);
            $objTrnNotifications = new TrnNotifications();
            $objTrnNotifications->setIsActive(1);
            $objTrnNotifications->setOrgCompany($this->orgCompany);
            $objTrnNotifications->setShortName($strNotificationName);
            $objTrnNotifications->setCreatedOn(new \DateTime());
            $objTrnNotifications->setAppUser($appUser);
            $objTrnNotifications->setDescription($strSystemNotification);
            $objTrnNotifications->setEmailSubject($strEmailSubject);
            $objTrnNotifications->setTrnCircle($this->trnCircle);
            $objTrnNotifications->setTrnCircleEvents($this->trnCircleEvents);
            $objTrnNotifications->setEmailContent($strEmailContent);
            $objTrnNotifications->setWhatsAppMessage($strWhatAppMsg);
            $objTrnNotifications->setTextMessage($strTextMessage);
            $objTrnNotifications->setMstNotificationStatus($objMstNotificationStatus);
            $objTrnNotifications->setPushNotifications($strPushNotification);
            $this->em->persist($objTrnNotifications);
            $this->em->flush();

            $this->sendEmail($objTrnNotifications);
        }

    }

    /**
     * @param $strEmailAddress
     */
    public function sendSubscriptionEmailNotLoggedInUser($strEmailAddress): void
    {
        $strNotificationName = 'Subscribe';
        $cmsNotification = $this->cmsNotificationRepository->findOneBy(['notificationName' => $strNotificationName, 'isActive' => 1]);
        $strPushNotification = $strTextMessage = $strWhatAppMsg = $strEmailContent = $strEmailSubject =
        $strSystemNotification = "";
        if (!empty($cmsNotification)) {
            if (!empty($cmsNotification->getSystemNotification())) {
                $strSystemNotification = $cmsNotification->getSystemNotification();
            }
            if (!empty($cmsNotification->getEmailSubject())) {
                $strEmailSubject = $cmsNotification->getEmailSubject();
                $strEmailSubject = strip_tags($strEmailSubject);
            }
            if (!empty($cmsNotification->getEmail())) {
                $strEmailContent = $cmsNotification->getEmail();
                $strEmailContent = str_replace('{username}', $strEmailAddress,$strEmailContent);
                $strEmailContent = str_replace('{social_media_widget}', '',$strEmailContent);
            }

            $objMstNotificationStatus = $this->mstNotificationStatusRepository->findOneBy(["notificationStatus" => 'Unread']);
            $objTrnNotifications = new TrnNotifications();
            $objTrnNotifications->setIsActive(1);
            $objTrnNotifications->setOrgCompany($this->orgCompany);
            $objTrnNotifications->setShortName($strNotificationName);
            $objTrnNotifications->setCreatedOn(new \DateTime());
            $objTrnNotifications->setAppUser(null);
            $objTrnNotifications->setDescription($strSystemNotification);
            $objTrnNotifications->setEmailSubject($strEmailSubject);
            $objTrnNotifications->setTrnCircle($this->trnCircle);
            $objTrnNotifications->setTrnCircleEvents($this->trnCircleEvents);
            $objTrnNotifications->setEmailContent($strEmailContent);
            $objTrnNotifications->setWhatsAppMessage($strWhatAppMsg);
            $objTrnNotifications->setTextMessage($strTextMessage);
            $objTrnNotifications->setMstNotificationStatus($objMstNotificationStatus);
            $objTrnNotifications->setPushNotifications($strPushNotification);
            $this->em->persist($objTrnNotifications);
            $this->em->flush();

            $this->sendEmailNoLoggedInUser($objTrnNotifications, $strEmailAddress);
        }

    }

    /**
     * @param AppUser $appUser
     * @param array $arrParameters
     * @return array
     */
    public function getLatestNotificationForUser(AppUser $appUser, $arrParameters = array())
    {
        $returnData = array();
        if (empty($arrParameters)) {
            $trnNotificationsArr = $this->trnNotificationsRepository->findBy(['isActive' => 1, 'appUser' => $appUser], ['id' => 'DESC']);
        } else {
            $query = $this->trnNotificationsRepository->createQueryBuilder('e')
                ->where(' e.appUser = :appUser')
                ->setParameter('appUser', $appUser);
            if (!empty($arrParameters['from']) && !empty($arrParameters['to'])) {
                $query->andWhere('e.createdOn between :fromDate AND :toDate')
                    ->setParameter('fromDate', $arrParameters['from'] . ' 00:00:00')
                    ->setParameter('toDate', $arrParameters['to'] . ' 23:59:59');
            }
            if (!empty($arrParameters['quicksearch'])) {
                $query->leftJoin('e.trnCircle', 'c')
                    ->leftJoin('e.trnCircleEvents', 'ce')
                    ->leftJoin('e.appUser', 'a')
                    ->leftJoin('a.appUserInfo', 'i')
                    ->andWhere('c.circle like :quicksearch OR ce.name like :quicksearch OR i.userFirstName like :quicksearch  OR i.userLastName like :quicksearch ')
                    ->setParameter('quicksearch', '%' . $arrParameters['quicksearch'] . '%');
            }

            if (!empty($arrParameters['projects_event'])) {
                if (in_array('projects', $arrParameters['projects_event']) !== false) {
                    $query->andWhere('e.trnCircle is not null');
                }
                if (in_array('events', $arrParameters['projects_event']) !== false) {
                    $query->andWhere('e.trnCircleEvents is not null');
                }
            }
            if (!empty($arrParameters['mstNotificationStatus'])) {
                switch ($arrParameters['mstNotificationStatus']) {
                    case 'All':
                    {
                        break;
                    }
                    case 'Unread':
                    {
                        $objMstNotificationStatusUnRead = $this->mstNotificationStatusRepository->findOneBy(['notificationStatus' => 'Unread']);
                        $query->andWhere('e.mstNotificationStatus = :mstNotificationStatus')
                            ->setParameter('mstNotificationStatus', $objMstNotificationStatusUnRead);
                        break;
                    }
                    case 'Read':
                    {
                        $objMstNotificationStatusUnRead = $this->mstNotificationStatusRepository->findOneBy(['notificationStatus' => 'Read']);
                        $query->andWhere('e.mstNotificationStatus = :mstNotificationStatus')
                            ->setParameter('mstNotificationStatus', $objMstNotificationStatusUnRead);
                        break;
                    }
                }
            }
            if (!empty($arrParameters['mstEventProductType'])) {
                $query->innerJoin('e.trnCircleEvents', 'te')
                    ->innerJoin('te.mstEventProductType', 'p')
                    ->andWhere('p.id in (:mstEventProductType_id)')
                    ->setParameter('mstEventProductType_id', $arrParameters['mstEventProductType']);
            }
            $query->orderBy('e.id', 'DESC');
            $trnNotificationsArr = $query->getQuery()->getResult();
        }
        $returnData = array();
        foreach ($trnNotificationsArr as $trnNotifications) {
            if (empty($trnNotifications->getDescription()))
                continue;
            $returnData[$trnNotifications->getId()] = array('notification' => $trnNotifications->getDescription(), 'date' =>
                $trnNotifications->getCreatedOn(), 'trnCircle' => $trnNotifications->getTrnCircle(),
                'trnCircleEvents' => $trnNotifications->getTrnCircleEvents(), 'mstNotificationStatus' =>
                    $trnNotifications->getMstNotificationStatus(), 'id' => $trnNotifications->getId());
        }
        return $returnData;
    }

    /**
     * @param AppUser $appUser
     */
    public function setAppUser(AppUser $appUser): void
    {
        $this->appUser = $appUser;
    }

    /**
     * @param TrnCircle $trnCircle
     */
    public function setTrnCircle(TrnCircle $trnCircle): void
    {
        $this->trnCircle = $trnCircle;
    }

    /**
     * @param TrnCircleEvents $trnCircleEvents
     */
    public function setTrnCircleEvents(TrnCircleEvents $trnCircleEvents): void
    {
        $this->trnCircleEvents = $trnCircleEvents;
    }

    /**
     * @param $nDonationAmount
     */
    public function setDonationAmount($nDonationAmount)
    {
        $this->donationAmount = $nDonationAmount;
    }

    /**
     * @param $strDonorName
     */
    public function setDonorName($strDonorName)
    {
        $this->donationAmount = $strDonorName;
    }

    /**
     * @param AppUser $appUser
     */
    public function setRequesterAppUser(AppUser $appUser)
    {
        $this->requesterAppUser = $appUser;
    }

    /**
     * @param int $nNotificationId
     */
    public function markNotificationAsRead(int $nNotificationId)
    {
        $objMstNotificationStatus = $this->mstNotificationStatusRepository->findOneBy(["notificationStatus" => 'Read']);
        $trnNotifications = $this->trnNotificationsRepository->find($nNotificationId);
        $trnNotifications->setMstNotificationStatus($objMstNotificationStatus);
        $this->em->persist($trnNotifications);
        $this->em->flush();
    }

    /**
     * @param CmsArticle $cmsArticle
     */
    public function setArticle(CmsArticle $cmsArticle)
    {
        $this->cmsArticle = $cmsArticle;
    }

    /**
     * @param $strNotificationName
     */
    public function doGCProcess($strNotificationName)
    {
        $arrBackEndUser = $this->appUserRepository->getBackEndUsers();
        foreach ($arrBackEndUser as $appUser) {
            $this->setAppUser($appUser);
            $this->doProcess($strNotificationName);
        }
    }
}