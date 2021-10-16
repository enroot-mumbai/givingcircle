<?php


namespace App\Service;

use App\Entity\Cms\CmsArticle;
use App\Entity\Marketing\MktPromotion;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstExchangeRate;
use App\Entity\Master\MstPaymentGateway;
use App\Entity\Master\MstTax;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnFundRaiserCircleEventSubEvents;
use App\Entity\Transaction\TrnOrder;
use App\Entity\Transaction\TrnOrderDetail;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class Cart
{
    private $em;
    private $security;
    private $commonHelper;
    private $session;

    public function __construct(EntityManagerInterface $em, CommonHelper $commonHelper,Security $security, SessionInterface $session) {
        $this->em = $em;
        $this->commonHelper = $commonHelper;
        $this->security = $security;
        $this->session = $session;
    }

    /**
     * @param $arrUserDetails
     * @param TrnCircleEvents|null $trnCircleEvent
     * @param TrnFundRaiserCircleEventSubEvents|null $trnFundRaiserCircleEventSubEvent
     * @param TrnCrowdFundEvent|null $trnCrowdFundEvent
     * @param AppUser|null $appUser
     * @param CmsArticle|null $changeMakerArticle
     * @throws \Exception
     */
    public function createCart($arrUserDetails, TrnCircleEvents $trnCircleEvent=null, TrnFundRaiserCircleEventSubEvents $trnFundRaiserCircleEventSubEvent= null, TrnCrowdFundEvent $trnCrowdFundEvent = null, AppUser $appUser = null, CmsArticle $changeMakerArticle = null )
    {
        $cartId = Uuid::uuid4()->toString();
        $this->session->set('cartId', $cartId);
        $trnOrder = new TrnOrder();
        $trnOrderDetail = new TrnOrderDetail();
        $tokenUserId = '';
        $tokenCircleEventId = '';
        $tokenChangeMakerArticleId = '';
        // Token / Order Generation Start
        if (!empty($appUser))
            $tokenUserId = $appUser->getId();

        $tokenTime = $this->commonHelper->tokenTime();

        if (!empty($changeMakerArticle)) {
            $tokenChangeMakerArticleId = $changeMakerArticle->getId();
        } else {
            $tokenCircleEventId = $trnCircleEvent->getId();
        }
        if (!empty($trnFundRaiserCircleEventSubEvent)) {
            $tokenTrnFundRaiserCircleEventSubEventsId = $trnFundRaiserCircleEventSubEvent->getId();
            $tokenNo = $tokenTime.''.$tokenCircleEventId.''.$tokenUserId.''.$tokenTrnFundRaiserCircleEventSubEventsId;
        } elseif(!empty($trnCrowdFundEvent)) {
            $tokenTrnCrowdFundEventId = $trnCrowdFundEvent->getId();
            $tokenNo = $tokenTime.''.$tokenCircleEventId.''.$tokenUserId.''.$tokenTrnCrowdFundEventId;
        } else if(!empty($tokenCircleEventId)) {
            $tokenNo = $tokenTime.''.$tokenCircleEventId.''.$tokenUserId;
        } else {
            $tokenNo = $tokenTime.''.$tokenChangeMakerArticleId.''.$tokenUserId;
        }
        // Token / Order Generation End
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $dateFormat = $now->format('dmy');
        $userTempOrderNo = ''.$dateFormat.'-'.$tokenUserId.'';
        $trnOrder->setCartId($cartId);
        $trnOrder->setCartTokenId(intval($tokenNo));
        $trnOrder->setOrderNo(intval($tokenNo));
        $objMstCurrency = $this->em->getRepository(MstCurrency::class)->findOneBy(array('iso3' => 'INR'));

        if (!empty($appUser)){
            $trnOrder->setAppUser($appUser);
            $trnOrder->setAppUserCategory($appUser->getAppUserCategory());
        }
        $trnOrder->setUserFirstName($arrUserDetails['fname-fund']);
        $trnOrder->setUserLastName($arrUserDetails['lname-fund']);
        $trnOrder->setUserEmail($arrUserDetails['email-id']);
        $trnOrder->setUserMobileNo($arrUserDetails['mobile-no']);
        $trnOrder->setUserPanNumber($arrUserDetails['panNum']);
        $trnOrder->setTextNote($arrUserDetails['txtNote']);
        if (!empty($arrUserDetails["anonymous"]))
            $trnOrder->setIsAnonymousDonation(1);
        else
            $trnOrder->setIsAnonymousDonation(0);
        $trnOrder->setAgreeTerms(1);
        $trnOrder->setUserOrderNo($userTempOrderNo);
        $trnOrder->setMstPaymentGateway($this->em->getRepository(MstPaymentGateway::class)->find(1));
        $trnOrder->setTotalAmount($arrUserDetails["contributionAmount"] );
        $trnOrder->setTransactionAmount($arrUserDetails["contributionAmount"] );

        if(!empty($changeMakerArticle)) {
            $trnOrder->setChangeMakerArticle($changeMakerArticle);
            $trnOrder->setIsGivingCircleDonation(1);
        } else {
            $trnOrder->setIsGivingCircleDonation(0);
        }

        if (!empty($trnCircleEvent)) {
            $trnOrder->setTrnCircleEvent($trnCircleEvent);
        }
        if (!empty($trnFundRaiserCircleEventSubEvent)) {
            $trnOrder->setTransactionCurrency($trnFundRaiserCircleEventSubEvent->getMstCurrencySubEvent());
            $trnOrder->setTrnFundRaiserCircleEventSubEvents($trnFundRaiserCircleEventSubEvent);
        }elseif(!empty($trnCrowdFundEvent)) {
            $trnOrder->setTransactionCurrency($trnCrowdFundEvent->getMstTargetAmountCurrency());
            $trnOrder->setTrnCrowdFundEvent($trnCrowdFundEvent);
        } else {
            $trnOrder->setTransactionCurrency($objMstCurrency);
        }
        $trnOrder->setPaymentMode('Online');
        if (!empty($appUser)) {
            $objAppUserInfo = $appUser->getAppUserInfo();
            // Shipping Address Information
            $trnOrder->setShippingAddressOne($objAppUserInfo->getAddress1());
            $trnOrder->setShippingAddressTwo($objAppUserInfo->getAddress2());
            $trnOrder->setShippingAddressPincode($objAppUserInfo->getPincode());
            $trnOrder->setShippingAddressCity($objAppUserInfo->getMstCity()->getCity());
            $trnOrder->setShippingAddressState($objAppUserInfo->getMstState()->getState());
            $trnOrder->setShippingAddressCountry($objAppUserInfo->getMstCountry());
            $trnOrder->setShippingAddressEmail($objAppUserInfo->getUserEmail());
            $trnOrder->setShippingAddressTelNumber($objAppUserInfo->getMobWithCountryCode());
            // Billing Address Information
            $trnOrder->setBillingAddressOne($objAppUserInfo->getAddress1());
            $trnOrder->setBillingAddressTwo($objAppUserInfo->getAddress2());
            $trnOrder->setBillingAddressPincode($objAppUserInfo->getPincode());
            $trnOrder->setBillingAddressCity($objAppUserInfo->getMstCity()->getCity());
            $trnOrder->setBillingAddressState($objAppUserInfo->getMstState()->getState());
            $trnOrder->setBillingAddressCountry($objAppUserInfo->getMstCountry());
            $trnOrder->setBillingAddressEmail($objAppUserInfo->getUserEmail());
            $trnOrder->setBillingAddressTelNumber($objAppUserInfo->getMobWithCountryCode());
        }
        $trnOrder->setCartDateTime(new DateTime('now', new DateTimeZone('UTC')));
        $trnOrder->setAppuserIPAddress($_SERVER['REMOTE_ADDR']);
        $trnOrder->setAppuserOSBrowserAgent($_SERVER['HTTP_USER_AGENT']);

        // Set TrnOrderDetail
        $trnOrderDetail->setTrnOrder($trnOrder);
        $trnOrderDetail->setContributionAmount($arrUserDetails["contributionAmount"]);

        if(!empty($changeMakerArticle)) {
            $trnOrderDetail->setChangeMakerArticle($changeMakerArticle);
        }
        if(!empty($trnCircleEvent)) {
            $trnOrderDetail->setTrnCircleEvent($trnCircleEvent);
        }
        if (!empty($trnFundRaiserCircleEventSubEvent)) {
            $trnOrderDetail->setContributionCurrency($trnFundRaiserCircleEventSubEvent->getMstCurrencySubEvent());
            $trnOrderDetail->setTrnFundRaiserCircleEventSubEvents($trnFundRaiserCircleEventSubEvent);
        } elseif(!empty($trnCrowdFundEvent)) {
            $trnOrderDetail->setContributionCurrency($trnCrowdFundEvent->getMstTargetAmountCurrency());
            $trnOrderDetail->setTrnCrowdFundEvent($trnCrowdFundEvent);
        } else {
            $trnOrderDetail->setContributionCurrency($objMstCurrency);
        }

        $this->em->persist($trnOrder);
        $this->em->persist($trnOrderDetail);
        $this->em->flush();
    }
}