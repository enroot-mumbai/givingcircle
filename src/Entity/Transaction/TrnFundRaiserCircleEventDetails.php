<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnFundRaiserCircleEventDetailsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnFundRaiserCircleEventDetailsRepository::class)
 * @ORM\Table("trnfundraisercircleeventdetails")
 */
class TrnFundRaiserCircleEventDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $causeOfFundRaiser;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $targetAmount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $forWhomRaisingFundFor;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $minContributionAmount;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCurrencyMinContribution;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $tellAStory;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnFundRaiserCircleEventDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircleEvents;

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
     * @ORM\ManyToOne(targetEntity=MstEventOccurrence::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstEventOccurrence;

    /**
     * @ORM\OneToMany(targetEntity=TrnFundRaiserCircleEventSubEvents::class, cascade={"persist", "remove"}, mappedBy="trnFundRaiserCircleEventDetails", orphanRemoval=true)
     */
    private $trnFundRaiserCircleEventSubEvents;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCurrencyTargetAmount;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventRecurringDetails::class, cascade={"persist","remove"}, mappedBy="trnFundRaiserCircleEventDetails")
     */
    private $trnCircleEventRecurringDetails;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDistributedEvent;

    /**
     * @ORM\Column(type="text")
     */
    private $purposeOfRaisingFunds;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $toDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUrgent;

    public function __construct()
    {
        $this->trnFundRaiserCircleEventSubEvents = new ArrayCollection();
        $this->trnCircleEventRecurringDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCauseOfFundRaiser(): ?string
    {
        return $this->causeOfFundRaiser;
    }

    public function setCauseOfFundRaiser(string $causeOfFundRaiser): self
    {
        $this->causeOfFundRaiser = $causeOfFundRaiser;

        return $this;
    }

    public function getTargetAmount(): ?string
    {
        return $this->targetAmount;
    }

    public function setTargetAmount(string $targetAmount): self
    {
        $this->targetAmount = $targetAmount;

        return $this;
    }

    public function getMstCurrentTargetAmount(): ?MstCurrency
    {
        return $this->mstCurrencyTargetAmount;
    }

    public function setMstCurrentTargetAmount(?MstCurrency $mstCurrentTargetAmount): self
    {
        $this->mstCurrentTargetAmount = $mstCurrentTargetAmount;

        return $this;
    }

    public function getForWhomRaisingFundFor(): ?string
    {
        return $this->forWhomRaisingFundFor;
    }

    public function setForWhomRaisingFundFor(string $forWhomRaisingFundFor): self
    {
        $this->forWhomRaisingFundFor = $forWhomRaisingFundFor;

        return $this;
    }

    public function getMinContributionAmount(): ?string
    {
        return $this->minContributionAmount;
    }

    public function setMinContributionAmount(?string $minContributionAmount): self
    {
        $this->minContributionAmount = $minContributionAmount;

        return $this;
    }

    public function getMstCurrencyMinContribution(): ?MstCurrency
    {
        return $this->mstCurrencyMinContribution;
    }

    public function setMstCurrencyMinContribution(?MstCurrency $mstCurrencyMinContribution): self
    {
        $this->mstCurrencyMinContribution = $mstCurrencyMinContribution;

        return $this;
    }

    public function getTellAStory(): ?string
    {
        return $this->tellAStory;
    }

    public function setTellAStory(string $tellAStory): self
    {
        $this->tellAStory = $tellAStory;

        return $this;
    }

    public function getMstCity(): ?MstCity
    {
        return $this->mstCity;
    }

    public function setMstCity(?MstCity $mstCity): self
    {
        $this->mstCity = $mstCity;

        return $this;
    }

    public function getTrnCircleEvents(): ?TrnCircleEvents
    {
        return $this->trnCircleEvents;
    }

    public function setTrnCircleEvents(?TrnCircleEvents $trnCircleEvents): self
    {
        $this->trnCircleEvents = $trnCircleEvents;

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

    public function getMstEventOccurrence(): ?MstEventOccurrence
    {
        return $this->mstEventOccurrence;
    }

    public function setMstEventOccurrence(?MstEventOccurrence $mstEventOccurrence): self
    {
        $this->mstEventOccurrence = $mstEventOccurrence;

        return $this;
    }

    /**
     * @return Collection|TrnFundRaiserCircleEventSubEvents[]
     */
    public function getTrnFundRaiserCircleEventSubEvents(): Collection
    {
        return $this->trnFundRaiserCircleEventSubEvents;
    }

    public function addTrnFundRaiserCircleEventSubEvent(TrnFundRaiserCircleEventSubEvents $trnFundRaiserCircleEventSubEvent): self
    {
        if (!$this->trnFundRaiserCircleEventSubEvents->contains($trnFundRaiserCircleEventSubEvent)) {
            $this->trnFundRaiserCircleEventSubEvents[] = $trnFundRaiserCircleEventSubEvent;
            $trnFundRaiserCircleEventSubEvent->setTrnFundRaiserCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnFundRaiserCircleEventSubEvent(TrnFundRaiserCircleEventSubEvents $trnFundRaiserCircleEventSubEvent): self
    {
        if ($this->trnFundRaiserCircleEventSubEvents->contains($trnFundRaiserCircleEventSubEvent)) {
            $this->trnFundRaiserCircleEventSubEvents->removeElement($trnFundRaiserCircleEventSubEvent);
            // set the owning side to null (unless already changed)
            if ($trnFundRaiserCircleEventSubEvent->getTrnFundRaiserCircleEventDetails() === $this) {
                $trnFundRaiserCircleEventSubEvent->setTrnFundRaiserCircleEventDetails(null);
            }
        }

        return $this;
    }

    public function setTrnFundRaiserCircleEventSubEvents(){
        $this->trnFundRaiserCircleEventSubEvents = new ArrayCollection();
    }

    public function getMstCurrencyTargetAmount(): ?MstCurrency
    {
        return $this->mstCurrencyTargetAmount;
    }

    public function setMstCurrencyTargetAmount(?MstCurrency $mstCurrencyTargetAmount): self
    {
        $this->mstCurrencyTargetAmount = $mstCurrencyTargetAmount;

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventRecurringDetails[]
     */
    public function getTrnCircleEventRecurringDetails(): Collection
    {
        return $this->trnCircleEventRecurringDetails;
    }

    public function addTrnCircleEventRecurringDetail(TrnCircleEventRecurringDetails $trnCircleEventRecurringDetail): self
    {
        if (!$this->trnCircleEventRecurringDetails->contains($trnCircleEventRecurringDetail)) {
            $this->trnCircleEventRecurringDetails[] = $trnCircleEventRecurringDetail;
            $trnCircleEventRecurringDetail->setTrnFundRaiserCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnCircleEventRecurringDetail(TrnCircleEventRecurringDetails $trnCircleEventRecurringDetail): self
    {
        if ($this->trnCircleEventRecurringDetails->contains($trnCircleEventRecurringDetail)) {
            $this->trnCircleEventRecurringDetails->removeElement($trnCircleEventRecurringDetail);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventRecurringDetail->getTrnFundRaiserCircleEventDetails() === $this) {
                $trnCircleEventRecurringDetail->setTrnFundRaiserCircleEventDetails(null);
            }
        }

        return $this;
    }

    public function getCreatedOn(): ?DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getIsDistributedEvent(): ?bool
    {
        return $this->isDistributedEvent;
    }

    public function setIsDistributedEvent(?bool $isDistributedEvent): self
    {
        $this->isDistributedEvent = $isDistributedEvent;

        return $this;
    }

    public function getPurposeOfRaisingFunds(): ?string
    {
        return $this->purposeOfRaisingFunds;
    }

    public function setPurposeOfRaisingFunds(string $purposeOfRaisingFunds): self
    {
        $this->purposeOfRaisingFunds = $purposeOfRaisingFunds;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getIsUrgent(): ?bool
    {
        if($this->isUrgent == null) {
            $this->isUrgent = 0;
        }
        return $this->isUrgent;
    }

    public function setIsUrgent(?bool $isUrgent): self
    {
        if($isUrgent == null) {
            $isUrgent = 0;
        }
        $this->isUrgent = $isUrgent;

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
}
