<?php

namespace App\Entity\Transaction;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstCurrency;
use App\Repository\Transaction\TrnOrderDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnOrderDetailRepository::class)
 * @ORM\Table("trnorderdetail")
 */
class TrnOrderDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnOrderDetails")
     */
    private $trnCircleEvent;

    /**
     * @ORM\ManyToOne(targetEntity=TrnFundRaiserCircleEventSubEvents::class, inversedBy="trnOrderDetails")
     */
    private $trnFundRaiserCircleEventSubEvents;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $contributionAmount;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $contributionCurrency;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discount;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $discountValueType;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $discountValue;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $discountAmountValue;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $discountAmountValueROE;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $discountCurrencyROE;

    /**
     * @ORM\ManyToOne(targetEntity=TrnOrder::class, inversedBy="trnOrderDetails")
     */
    private $trnOrder;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCrowdFundEvent::class, inversedBy="trnOrderDetails")
     */
    private $trnCrowdFundEvent;

    /**
     * @ORM\ManyToOne(targetEntity=CmsArticle::class, inversedBy="trnOrderDetails")
     */
    private $changeMakerArticle;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContributionAmount(): ?string
    {
        return $this->contributionAmount;
    }

    public function setContributionAmount(?string $contributionAmount): self
    {
        $this->contributionAmount = $contributionAmount;

        return $this;
    }

    public function getContributionCurrency(): ?MstCurrency
    {
        return $this->contributionCurrency;
    }

    public function setContributionCurrency(?MstCurrency $contributionCurrency): self
    {
        $this->contributionCurrency = $contributionCurrency;

        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getDiscountValueType(): ?string
    {
        return $this->discountValueType;
    }

    public function setDiscountValueType(?string $discountValueType): self
    {
        $this->discountValueType = $discountValueType;

        return $this;
    }

    public function getDiscountValue(): ?string
    {
        return $this->discountValue;
    }

    public function setDiscountValue(?string $discountValue): self
    {
        $this->discountValue = $discountValue;

        return $this;
    }

    public function getDiscountAmountValue(): ?string
    {
        return $this->discountAmountValue;
    }

    public function setDiscountAmountValue(?string $discountAmountValue): self
    {
        $this->discountAmountValue = $discountAmountValue;

        return $this;
    }

    public function getDiscountAmountValueROE(): ?string
    {
        return $this->discountAmountValueROE;
    }

    public function setDiscountAmountValueROE(?string $discountAmountValueROE): self
    {
        $this->discountAmountValueROE = $discountAmountValueROE;

        return $this;
    }

    public function getDiscountCurrencyROE(): ?MstCurrency
    {
        return $this->discountCurrencyROE;
    }

    public function setDiscountCurrencyROE(?MstCurrency $discountCurrencyROE): self
    {
        $this->discountCurrencyROE = $discountCurrencyROE;

        return $this;
    }

    public function getTrnOrder(): ?TrnOrder
    {
        return $this->trnOrder;
    }

    public function setTrnOrder(?TrnOrder $trnOrder): self
    {
        $this->trnOrder = $trnOrder;

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
