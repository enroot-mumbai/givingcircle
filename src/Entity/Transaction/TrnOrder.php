<?php

namespace App\Entity\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstPaymentGateway;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Repository\Transaction\TrnOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnOrderRepository::class)
 * @ORM\Table("trnorder")
 */
class TrnOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="guid")
     */
    private $cartId;

    /**
     * @ORM\Column(type="bigint")
     */
    private $orderNo;

    /**
     * @ORM\Column(type="bigint")
     */
    private $cartTokenId;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userEmail;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $userMobileNo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $agreeTerms;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $promotionCode;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $transactionId;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $transactionStatus;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $transactionLog = [];

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $verifyPaymentStatus;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $verifyPaymentLog = [];

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $promotionAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $taxAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $serviceChargeAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $referralAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalAmountBeforeTax;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalAmountBeforeTaxROE;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $totalAmountBeforeTaxCurrencyROE;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $transactionAmount;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $transactionCurrency;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $fromCurrency;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $toCurrency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $rateOfExchange;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $exchangeMarkupType;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $exchangeMarkupValue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddressOne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddressTwo;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $shippingAddressPincode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddressState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     */
    private $shippingAddressCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shippingAddressEmail;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $shippingAddressTelNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingUserFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingUserLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingAddressOne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingAddressTwo;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $billingAddressPincode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingAddressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $billingAddressState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     */
    private $billingAddressCountry;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $billingAddressEmail;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $billingAddressTelNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cartDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $orderDateTime;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $failedOrderNo;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $userOrderNo;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $userInvoiceNo;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $appUserIPAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $appUserOSBrowserAgent;

    /**
     * @ORM\ManyToOne(targetEntity=AppUserCategory::class)
     */
    private $appUserCategory;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnOrders")
     */
    private $trnCircleEvent;

    /**
     * @ORM\ManyToOne(targetEntity=TrnFundRaiserCircleEventSubEvents::class, inversedBy="trnOrders")
     */
    private $trnFundRaiserCircleEventSubEvents;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAnonymousDonation;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $userPanNumber;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textNote;

    /**
     * @ORM\ManyToOne(targetEntity=MstPaymentGateway::class)
     */
    private $mstPaymentGateway;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrderDetail::class, mappedBy="trnOder")
     */
    private $trnOrderDetails;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCrowdFundEvent::class, inversedBy="trnOrders")
     */
    private $trnCrowdFundEvent;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $paymentMode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isGivingCircleDonation;


    /**
     * @ORM\ManyToOne(targetEntity=CmsArticle::class)
     */
    private $changeMakerArticle;

    public function __construct()
    {
        $this->trnOrderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCartId(): ?string
    {
        return $this->cartId;
    }

    public function setCartId(string $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getOrderNo(): ?string
    {
        return $this->orderNo;
    }

    public function setOrderNo(string $orderNo): self
    {
        $this->orderNo = $orderNo;

        return $this;
    }

    public function getCartTokenId(): ?string
    {
        return $this->cartTokenId;
    }

    public function setCartTokenId(string $cartTokenId): self
    {
        $this->cartTokenId = $cartTokenId;

        return $this;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): self
    {
        $this->appUser = $appUser;

        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(?string $userFirstName): self
    {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(?string $userLastName): self
    {
        $this->userLastName = $userLastName;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(?string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserMobileNo(): ?string
    {
        return $this->userMobileNo;
    }

    public function setUserMobileNo(?string $userMobileNo): self
    {
        $this->userMobileNo = $userMobileNo;

        return $this;
    }

    public function getAgreeTerms(): ?bool
    {
        return $this->agreeTerms;
    }

    public function setAgreeTerms(?bool $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;

        return $this;
    }

    public function getPromotionCode(): ?string
    {
        return $this->promotionCode;
    }

    public function setPromotionCode(?string $promotionCode): self
    {
        $this->promotionCode = $promotionCode;

        return $this;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getTransactionStatus(): ?string
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(?string $transactionStatus): self
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    public function getTransactionLog(): ?array
    {
        return $this->transactionLog;
    }

    public function setTransactionLog(?array $transactionLog): self
    {
        $this->transactionLog = $transactionLog;

        return $this;
    }

    public function getVerifyPaymentStatus(): ?string
    {
        return $this->verifyPaymentStatus;
    }

    public function setVerifyPaymentStatus(?string $verifyPaymentStatus): self
    {
        $this->verifyPaymentStatus = $verifyPaymentStatus;

        return $this;
    }

    public function getVerifyPaymentLog(): ?array
    {
        return $this->verifyPaymentLog;
    }

    public function setVerifyPaymentLog(?array $verifyPaymentLog): self
    {
        $this->verifyPaymentLog = $verifyPaymentLog;

        return $this;
    }

    public function getPromotionAmount(): ?string
    {
        return $this->promotionAmount;
    }

    public function setPromotionAmount(?string $promotionAmount): self
    {
        $this->promotionAmount = $promotionAmount;

        return $this;
    }

    public function getTaxAmount(): ?string
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(?string $taxAmount): self
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    public function getServiceChargeAmount(): ?string
    {
        return $this->serviceChargeAmount;
    }

    public function setServiceChargeAmount(?string $serviceChargeAmount): self
    {
        $this->serviceChargeAmount = $serviceChargeAmount;

        return $this;
    }

    public function getReferralAmount(): ?string
    {
        return $this->referralAmount;
    }

    public function setReferralAmount(?string $referralAmount): self
    {
        $this->referralAmount = $referralAmount;

        return $this;
    }

    public function getTotalAmountBeforeTax(): ?string
    {
        return $this->totalAmountBeforeTax;
    }

    public function setTotalAmountBeforeTax(?string $totalAmountBeforeTax): self
    {
        $this->totalAmountBeforeTax = $totalAmountBeforeTax;

        return $this;
    }

    public function getTotalAmountBeforeTaxROE(): ?string
    {
        return $this->totalAmountBeforeTaxROE;
    }

    public function setTotalAmountBeforeTaxROE(?string $totalAmountBeforeTaxROE): self
    {
        $this->totalAmountBeforeTaxROE = $totalAmountBeforeTaxROE;

        return $this;
    }

    public function getTotalAmountBeforeTaxCurrencyROE(): ?MstCurrency
    {
        return $this->totalAmountBeforeTaxCurrencyROE;
    }

    public function setTotalAmountBeforeTaxCurrencyROE(?MstCurrency $totalAmountBeforeTaxCurrencyROE): self
    {
        $this->totalAmountBeforeTaxCurrencyROE = $totalAmountBeforeTaxCurrencyROE;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getTransactionAmount(): ?string
    {
        return $this->transactionAmount;
    }

    public function setTransactionAmount(?string $transactionAmount): self
    {
        $this->transactionAmount = $transactionAmount;

        return $this;
    }

    public function getTransactionCurrency(): ?MstCurrency
    {
        return $this->transactionCurrency;
    }

    public function setTransactionCurrency(?MstCurrency $transactionCurrency): self
    {
        $this->transactionCurrency = $transactionCurrency;

        return $this;
    }

    public function getFromCurrency(): ?MstCurrency
    {
        return $this->fromCurrency;
    }

    public function setFromCurrency(?MstCurrency $fromCurrency): self
    {
        $this->fromCurrency = $fromCurrency;

        return $this;
    }

    public function getToCurrency(): ?MstCurrency
    {
        return $this->toCurrency;
    }

    public function setToCurrency(?MstCurrency $toCurrency): self
    {
        $this->toCurrency = $toCurrency;

        return $this;
    }

    public function getRateOfExchange(): ?string
    {
        return $this->rateOfExchange;
    }

    public function setRateOfExchange(?string $rateOfExchange): self
    {
        $this->rateOfExchange = $rateOfExchange;

        return $this;
    }

    public function getExchangeMarkupType(): ?string
    {
        return $this->exchangeMarkupType;
    }

    public function setExchangeMarkupType(?string $exchangeMarkupType): self
    {
        $this->exchangeMarkupType = $exchangeMarkupType;

        return $this;
    }

    public function getExchangeMarkupValue(): ?string
    {
        return $this->exchangeMarkupValue;
    }

    public function setExchangeMarkupValue(?string $exchangeMarkupValue): self
    {
        $this->exchangeMarkupValue = $exchangeMarkupValue;

        return $this;
    }

    public function getShippingAddressOne(): ?string
    {
        return $this->shippingAddressOne;
    }

    public function setShippingAddressOne(?string $shippingAddressOne): self
    {
        $this->shippingAddressOne = $shippingAddressOne;

        return $this;
    }

    public function getShippingAddressTwo(): ?string
    {
        return $this->shippingAddressTwo;
    }

    public function setShippingAddressTwo(?string $shippingAddressTwo): self
    {
        $this->shippingAddressTwo = $shippingAddressTwo;

        return $this;
    }

    public function getShippingAddressPincode(): ?string
    {
        return $this->shippingAddressPincode;
    }

    public function setShippingAddressPincode(?string $shippingAddressPincode): self
    {
        $this->shippingAddressPincode = $shippingAddressPincode;

        return $this;
    }

    public function getShippingAddressCity(): ?string
    {
        return $this->shippingAddressCity;
    }

    public function setShippingAddressCity(?string $shippingAddressCity): self
    {
        $this->shippingAddressCity = $shippingAddressCity;

        return $this;
    }

    public function getShippingAddressState(): ?string
    {
        return $this->shippingAddressState;
    }

    public function setShippingAddressState(?string $shippingAddressState): self
    {
        $this->shippingAddressState = $shippingAddressState;

        return $this;
    }

    public function getShippingAddressCountry(): ?MstCountry
    {
        return $this->shippingAddressCountry;
    }

    public function setShippingAddressCountry(?MstCountry $shippingAddressCountry): self
    {
        $this->shippingAddressCountry = $shippingAddressCountry;

        return $this;
    }

    public function getShippingAddressEmail(): ?string
    {
        return $this->shippingAddressEmail;
    }

    public function setShippingAddressEmail(?string $shippingAddressEmail): self
    {
        $this->shippingAddressEmail = $shippingAddressEmail;

        return $this;
    }

    public function getShippingAddressTelNumber(): ?string
    {
        return $this->shippingAddressTelNumber;
    }

    public function setShippingAddressTelNumber(?string $shippingAddressTelNumber): self
    {
        $this->shippingAddressTelNumber = $shippingAddressTelNumber;

        return $this;
    }

    public function getBillingUserFirstName(): ?string
    {
        return $this->billingUserFirstName;
    }

    public function setBillingUserFirstName(?string $billingUserFirstName): self
    {
        $this->billingUserFirstName = $billingUserFirstName;

        return $this;
    }

    public function getBillingUserLastName(): ?string
    {
        return $this->billingUserLastName;
    }

    public function setBillingUserLastName(?string $billingUserLastName): self
    {
        $this->billingUserLastName = $billingUserLastName;

        return $this;
    }

    public function getBillingAddressOne(): ?string
    {
        return $this->billingAddressOne;
    }

    public function setBillingAddressOne(?string $billingAddressOne): self
    {
        $this->billingAddressOne = $billingAddressOne;

        return $this;
    }

    public function getBillingAddressTwo(): ?string
    {
        return $this->billingAddressTwo;
    }

    public function setBillingAddressTwo(?string $billingAddressTwo): self
    {
        $this->billingAddressTwo = $billingAddressTwo;

        return $this;
    }

    public function getBillingAddressPincode(): ?string
    {
        return $this->billingAddressPincode;
    }

    public function setBillingAddressPincode(?string $billingAddressPincode): self
    {
        $this->billingAddressPincode = $billingAddressPincode;

        return $this;
    }

    public function getBillingAddressCity(): ?string
    {
        return $this->billingAddressCity;
    }

    public function setBillingAddressCity(?string $billingAddressCity): self
    {
        $this->billingAddressCity = $billingAddressCity;

        return $this;
    }

    public function getBillingAddressState(): ?string
    {
        return $this->billingAddressState;
    }

    public function setBillingAddressState(?string $billingAddressState): self
    {
        $this->billingAddressState = $billingAddressState;

        return $this;
    }

    public function getBillingAddressCountry(): ?MstCountry
    {
        return $this->billingAddressCountry;
    }

    public function setBillingAddressCountry(?MstCountry $billingAddressCountry): self
    {
        $this->billingAddressCountry = $billingAddressCountry;

        return $this;
    }

    public function getBillingAddressEmail(): ?string
    {
        return $this->billingAddressEmail;
    }

    public function setBillingAddressEmail(?string $billingAddressEmail): self
    {
        $this->billingAddressEmail = $billingAddressEmail;

        return $this;
    }

    public function getBillingAddressTelNumber(): ?string
    {
        return $this->billingAddressTelNumber;
    }

    public function setBillingAddressTelNumber(?string $billingAddressTelNumber): self
    {
        $this->billingAddressTelNumber = $billingAddressTelNumber;

        return $this;
    }

    public function getCartDateTime(): ?\DateTimeInterface
    {
        return $this->cartDateTime;
    }

    public function setCartDateTime(?\DateTimeInterface $cartDateTime): self
    {
        $this->cartDateTime = $cartDateTime;

        return $this;
    }

    public function getOrderDateTime(): ?\DateTimeInterface
    {
        return $this->orderDateTime;
    }

    public function setOrderDateTime(?\DateTimeInterface $orderDateTime): self
    {
        $this->orderDateTime = $orderDateTime;

        return $this;
    }

    public function getFailedOrderNo(): ?string
    {
        return $this->failedOrderNo;
    }

    public function setFailedOrderNo(?string $failedOrderNo): self
    {
        $this->failedOrderNo = $failedOrderNo;

        return $this;
    }

    public function getUserOrderNo(): ?string
    {
        return $this->userOrderNo;
    }

    public function setUserOrderNo(?string $userOrderNo): self
    {
        $this->userOrderNo = $userOrderNo;

        return $this;
    }

    public function getUserInvoiceNo(): ?string
    {
        return $this->userInvoiceNo;
    }

    public function setUserInvoiceNo(?string $userInvoiceNo): self
    {
        $this->userInvoiceNo = $userInvoiceNo;

        return $this;
    }

    public function getAppUserIPAddress(): ?string
    {
        return $this->appUserIPAddress;
    }

    public function setAppUserIPAddress(?string $appUserIPAddress): self
    {
        $this->appUserIPAddress = $appUserIPAddress;

        return $this;
    }

    public function getAppUserOSBrowserAgent(): ?string
    {
        return $this->appUserOSBrowserAgent;
    }

    public function setAppUserOSBrowserAgent(?string $appUserOSBrowserAgent): self
    {
        $this->appUserOSBrowserAgent = $appUserOSBrowserAgent;

        return $this;
    }

    public function getAppUserCategory(): ?AppUserCategory
    {
        return $this->appUserCategory;
    }

    public function setAppUserCategory(?AppUserCategory $appUserCategory): self
    {
        $this->appUserCategory = $appUserCategory;

        return $this;
    }

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

        return $this;
    }

    public function getTrnFundRaiserCircleEventSubEvents(): ?TrnFundRaiserCircleEventSubEvents
    {
        return $this->trnFundRaiserCircleEventSubEvents;
    }

    public function setTrnFundRaiserCircleEventSubEvents(?TrnFundRaiserCircleEventSubEvents $trnFundRaiserCircleEventSubEvents): self
    {
        $this->trnFundRaiserCircleEventSubEvents = $trnFundRaiserCircleEventSubEvents;

        return $this;
    }

    public function getIsAnonymousDonation(): ?bool
    {
        return $this->isAnonymousDonation;
    }

    public function setIsAnonymousDonation(?bool $isAnonymousDonation): self
    {
        $this->isAnonymousDonation = $isAnonymousDonation;

        return $this;
    }

    public function getUserPanNumber(): ?string
    {
        return $this->userPanNumber;
    }

    public function setUserPanNumber(?string $userPanNumber): self
    {
        $this->userPanNumber = $userPanNumber;

        return $this;
    }

    public function getTextNote(): ?string
    {
        return $this->textNote;
    }

    public function setTextNote(?string $textNote): self
    {
        $this->textNote = $textNote;

        return $this;
    }

    public function getMstPaymentGateway(): ?MstPaymentGateway
    {
        return $this->mstPaymentGateway;
    }

    public function setMstPaymentGateway(?MstPaymentGateway $mstPaymentGateway): self
    {
        $this->mstPaymentGateway = $mstPaymentGateway;

        return $this;
    }

    /**
     * @return Collection|TrnOrderDetail[]
     */
    public function getTrnOrderDetails(): Collection
    {
        return $this->trnOrderDetails;
    }

    public function addTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if (!$this->trnOrderDetails->contains($trnOrderDetail)) {
            $this->trnOrderDetails[] = $trnOrderDetail;
            $trnOrderDetail->setTrnOder($this);
        }

        return $this;
    }

    public function removeTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if ($this->trnOrderDetails->removeElement($trnOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnOrderDetail->getTrnOder() === $this) {
                $trnOrderDetail->setTrnOder(null);
            }
        }

        return $this;
    }

    public function getTrnCrowdFundEvent(): ?TrnCrowdFundEvent
    {
        return $this->trnCrowdFundEvent;
    }

    public function setTrnCrowdFundEvent(?TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        $this->trnCrowdFundEvent = $trnCrowdFundEvent;

        return $this;
    }

    public function getPaymentMode(): ?string
    {
        return $this->paymentMode;
    }

    public function setPaymentMode(?string $paymentMode): self
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    public function getIsGivingCircleDonation(): ?bool
    {
        return $this->isGivingCircleDonation;
    }

    public function setIsGivingCircleDonation(?bool $isGivingCircleDonation): self
    {
        $this->isGivingCircleDonation = $isGivingCircleDonation;

        return $this;
    }

    public function getChangeMakerArticle(): ?CmsArticle
    {
        return $this->changeMakerArticle;
    }

    public function setChangeMakerArticle(?CmsArticle $changeMakerArticle): self
    {
        $this->changeMakerArticle = $changeMakerArticle;

        return $this;
    }
}
