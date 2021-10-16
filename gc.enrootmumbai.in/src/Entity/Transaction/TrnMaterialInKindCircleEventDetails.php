<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnMaterialInKindCircleEventDetailsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrnMaterialInKindCircleEventDetailsRepository::class)
 * @ORM\Table("trnmaterialinkindcircleeventdetails")
 */
class TrnMaterialInKindCircleEventDetails
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
    private $causeOfEvent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $itemsRequired;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $tellAStory;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnMaterialInKindCircleEventDetails")
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
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstEventOccurrence;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialInKindCircleEventSubEvents::class, cascade={"persist", "remove"}, mappedBy="trnMaterialInKindCircleEventDetails", orphanRemoval=true)
     */
    private $trnMaterialInKindCircleEventSubEvents;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialInKindCircleEventCollectionCentre::class, cascade={"persist","remove"}, mappedBy="trnMaterialInKindCircleEventDetails", orphanRemoval=true)
     */
    private $trnMaterialInKindCircleEventCollectionCentres;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialReceivedAtCollectionCentre::class, mappedBy="trnMaterialInKindCircleEventDetails")
     */
    private $trnMaterialReceivedAtCollectionCentres;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventRecurringDetails::class, cascade={"persist", "remove"}, mappedBy="trnMaterialInKindCircleEventDetails")
     */
    private $trnCircleEventRecurringDetails;

    /**
     * @ORM\Column(type="date")
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date")
     */
    private $toDate;

    public function __construct()
    {
        $this->trnMaterialInKindCircleEventSubEvents = new ArrayCollection();
        $this->trnMaterialInKindCircleEventCollectionCentres = new ArrayCollection();
        $this->trnMaterialReceivedAtCollectionCentres = new ArrayCollection();
        $this->trnCircleEventRecurringDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCauseOfEvent(): ?string
    {
        return $this->causeOfEvent;
    }

    public function setCauseOfEvent(string $causeOfEvent): self
    {
        $this->causeOfEvent = $causeOfEvent;

        return $this;
    }

    public function getItemsRequired(): ?string
    {
        return $this->itemsRequired;
    }

    public function setItemsRequired(string $itemsRequired): self
    {
        $this->itemsRequired = $itemsRequired;

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

    public function getCreatedOn(): ?DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return Collection|TrnMaterialInKindCircleEventSubEvents[]
     */
    public function getTrnMaterialInKindCircleEventSubEvents(): Collection
    {
        return $this->trnMaterialInKindCircleEventSubEvents;
    }

    public function addTrnMaterialInKindCircleEventSubEvent(TrnMaterialInKindCircleEventSubEvents $trnMaterialInKindCircleEventSubEvent): self
    {
        if (!$this->trnMaterialInKindCircleEventSubEvents->contains($trnMaterialInKindCircleEventSubEvent)) {
            $this->trnMaterialInKindCircleEventSubEvents[] = $trnMaterialInKindCircleEventSubEvent;
            $trnMaterialInKindCircleEventSubEvent->setTrnMaterialInKindCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnMaterialInKindCircleEventSubEvent(TrnMaterialInKindCircleEventSubEvents $trnMaterialInKindCircleEventSubEvent): self
    {
        if ($this->trnMaterialInKindCircleEventSubEvents->contains($trnMaterialInKindCircleEventSubEvent)) {
            $this->trnMaterialInKindCircleEventSubEvents->removeElement($trnMaterialInKindCircleEventSubEvent);
            // set the owning side to null (unless already changed)
            if ($trnMaterialInKindCircleEventSubEvent->getTrnMaterialInKindCircleEventDetails() === $this) {
                $trnMaterialInKindCircleEventSubEvent->setTrnMaterialInKindCircleEventDetails(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnMaterialInKindCircleEventCollectionCentre[]
     */
    public function getTrnMaterialInKindCircleEventCollectionCentres(): Collection
    {
        return $this->trnMaterialInKindCircleEventCollectionCentres;
    }

    public function addTrnMaterialInKindCircleEventCollectionCentre(TrnMaterialInKindCircleEventCollectionCentre $trnMaterialInKindCircleEventCollectionCentre): self
    {
        if (!$this->trnMaterialInKindCircleEventCollectionCentres->contains($trnMaterialInKindCircleEventCollectionCentre)) {
            $this->trnMaterialInKindCircleEventCollectionCentres[] = $trnMaterialInKindCircleEventCollectionCentre;
            $trnMaterialInKindCircleEventCollectionCentre->setTrnMaterialInKindCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnMaterialInKindCircleEventCollectionCentre(TrnMaterialInKindCircleEventCollectionCentre $trnMaterialInKindCircleEventCollectionCentre): self
    {
        if ($this->trnMaterialInKindCircleEventCollectionCentres->contains($trnMaterialInKindCircleEventCollectionCentre)) {
            $this->trnMaterialInKindCircleEventCollectionCentres->removeElement($trnMaterialInKindCircleEventCollectionCentre);
            // set the owning side to null (unless already changed)
            if ($trnMaterialInKindCircleEventCollectionCentre->getTrnMaterialInKindCircleEventDetails() === $this) {
                $trnMaterialInKindCircleEventCollectionCentre->setTrnMaterialInKindCircleEventDetails(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnMaterialReceivedAtCollectionCentre[]
     */
    public function getTrnMaterialReceivedAtCollectionCentres(): Collection
    {
        return $this->trnMaterialReceivedAtCollectionCentres;
    }

    public function addTrnMaterialReceivedAtCollectionCentre(TrnMaterialReceivedAtCollectionCentre $trnMaterialReceivedAtCollectionCentre): self
    {
        if (!$this->trnMaterialReceivedAtCollectionCentres->contains($trnMaterialReceivedAtCollectionCentre)) {
            $this->trnMaterialReceivedAtCollectionCentres[] = $trnMaterialReceivedAtCollectionCentre;
            $trnMaterialReceivedAtCollectionCentre->setTrnMaterialInKindCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnMaterialReceivedAtCollectionCentre(TrnMaterialReceivedAtCollectionCentre $trnMaterialReceivedAtCollectionCentre): self
    {
        if ($this->trnMaterialReceivedAtCollectionCentres->contains($trnMaterialReceivedAtCollectionCentre)) {
            $this->trnMaterialReceivedAtCollectionCentres->removeElement($trnMaterialReceivedAtCollectionCentre);
            // set the owning side to null (unless already changed)
            if ($trnMaterialReceivedAtCollectionCentre->getTrnMaterialInKindCircleEventDetails() === $this) {
                $trnMaterialReceivedAtCollectionCentre->setTrnMaterialInKindCircleEventDetails(null);
            }
        }

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
            $trnCircleEventRecurringDetail->setTrnMaterialInKindCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnCircleEventRecurringDetail(TrnCircleEventRecurringDetails $trnCircleEventRecurringDetail): self
    {
        if ($this->trnCircleEventRecurringDetails->contains($trnCircleEventRecurringDetail)) {
            $this->trnCircleEventRecurringDetails->removeElement($trnCircleEventRecurringDetail);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventRecurringDetail->getTrnMaterialInKindCircleEventDetails() === $this) {
                $trnCircleEventRecurringDetail->setTrnMaterialInKindCircleEventDetails(null);
            }
        }

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

    public function setToDate(?\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

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
