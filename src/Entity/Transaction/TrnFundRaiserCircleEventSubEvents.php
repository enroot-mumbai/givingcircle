<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCurrency;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnFundRaiserCircleEventSubEventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnFundRaiserCircleEventSubEventsRepository::class)
 * @ORM\Table("trnfundraisercircleeventsubevents")
 */
class TrnFundRaiserCircleEventSubEvents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnFundRaiserCircleEventDetails::class, inversedBy="trnFundRaiserCircleEventSubEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnFundRaiserCircleEventDetails;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $subEventName;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCurrencySubEvent;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $subEventTargetAmount;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $timePeriodSupported;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $noOfBeneficiaries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subEventRemarks;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrder::class, mappedBy="trnFundRaiserCircleEventSubEvents")
     */
    private $trnOrders;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrderDetail::class, mappedBy="trnFundRaiserCircleEventSubEvents")
     */
    private $trnOrderDetails;

    public function __construct()
    {
        $this->trnOrders = new ArrayCollection();
        $this->trnOrderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnFundRaiserCircleEventDetails(): ?TrnFundRaiserCircleEventDetails
    {
        return $this->trnFundRaiserCircleEventDetails;
    }

    public function setTrnFundRaiserCircleEventDetails(?TrnFundRaiserCircleEventDetails $trnFundRaiserCircleEventDetails): self
    {
        $this->trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetails;

        return $this;
    }

    public function getSubEventName(): ?string
    {
        return $this->subEventName;
    }

    public function setSubEventName(string $subEventName): self
    {
        $this->subEventName = $subEventName;

        return $this;
    }

    public function getMstCurrencySubEvent(): ?MstCurrency
    {
        return $this->mstCurrencySubEvent;
    }

    public function setMstCurrencySubEvent(?MstCurrency $mstCurrencySubEvent): self
    {
        $this->mstCurrencySubEvent = $mstCurrencySubEvent;

        return $this;
    }

    public function getSubEventTargetAmount(): ?string
    {
        return $this->subEventTargetAmount;
    }

    public function setSubEventTargetAmount(string $subEventTargetAmount): self
    {
        $this->subEventTargetAmount = $subEventTargetAmount;

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

    public function getOrgCompany(): ?OrgCompany
    {
        return $this->orgCompany;
    }

    public function setOrgCompany(?OrgCompany $orgCompany): self
    {
        $this->orgCompany = $orgCompany;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getTimePeriodSupported(): ?string
    {
        return $this->timePeriodSupported;
    }

    public function setTimePeriodSupported(?string $timePeriodSupported): self
    {
        $this->timePeriodSupported = $timePeriodSupported;

        return $this;
    }

    public function getNoOfBeneficiaries(): ?string
    {
        return $this->noOfBeneficiaries;
    }

    public function setNoOfBeneficiaries(?string $noOfBeneficiaries): self
    {
        $this->noOfBeneficiaries = $noOfBeneficiaries;

        return $this;
    }

    public function getSubEventRemarks(): ?string
    {
        return $this->subEventRemarks;
    }

    public function setSubEventRemarks(?string $subEventRemarks): self
    {
        $this->subEventRemarks = $subEventRemarks;

        return $this;
    }
    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return Collection|TrnOrder[]
     */
    public function getTrnOrders(): Collection
    {
        return $this->trnOrders;
    }

    public function addTrnOrder(TrnOrder $trnOrder): self
    {
        if (!$this->trnOrders->contains($trnOrder)) {
            $this->trnOrders[] = $trnOrder;
            $trnOrder->setTrnFundRaiserCircleEventSubEvents($this);
        }

        return $this;
    }

    public function removeTrnOrder(TrnOrder $trnOrder): self
    {
        if ($this->trnOrders->removeElement($trnOrder)) {
            // set the owning side to null (unless already changed)
            if ($trnOrder->getTrnFundRaiserCircleEventSubEvents() === $this) {
                $trnOrder->setTrnFundRaiserCircleEventSubEvents(null);
            }
        }

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
            $trnOrderDetail->setTrnFundRaiserCircleEventSubEvents($this);
        }

        return $this;
    }

    public function removeTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if ($this->trnOrderDetails->removeElement($trnOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnOrderDetail->getTrnFundRaiserCircleEventSubEvents() === $this) {
                $trnOrderDetail->setTrnFundRaiserCircleEventSubEvents(null);
            }
        }

        return $this;
    }
}
