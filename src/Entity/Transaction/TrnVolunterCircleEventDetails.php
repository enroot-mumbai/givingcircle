<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreasInCity;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstPlaceOfWork;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Master\MstSkillSet;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventDetailsRepository::class)
 * @ORM\Table("trnvoluntercircleeventdetails")
 */
class TrnVolunterCircleEventDetails
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
    private $workDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $keyResponsibility;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $targetNumberOfVoluntersRequired;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnVolunterCircleEventDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircleEvents;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity=MstPlaceOfWork::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstPlaceOfWork;

    /**
     * @ORM\ManyToOne(targetEntity=MstEventOccurrence::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstEventOccurrence;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventOnSiteAddress::class, cascade={"persist", "remove"}, mappedBy="trnVolunterCircleEventDetails", orphanRemoval=true)
     */
    private $trnVolunterCircleEventOnSiteAddresses;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventSubEvents::class, cascade={"persist", "remove"}, mappedBy="trnVolunterCircleEventDetails", orphanRemoval=true)
     */
    private $trnVolunterCircleEventSubEvents;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventVolunterDetails::class, cascade={"persist", "remove"}, mappedBy="trnVolunterCircleEventDetails", orphanRemoval=true)
     */
    private $trnVolunterCircleEventVolunterDetails;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToMany(targetEntity=MstSkillSet::class)
     */
    private $mstSkillSet;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventRecurringDetails::class, cascade={"persist", "remove"}, mappedBy="trnVolunterCircleEventDetails", orphanRemoval=true)
     */
    private $trnCircleEventRecurringDetails;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("\DateTimeInterface")
     *
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("\DateTimeInterface")
     */
    private $toDate;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fromTime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $toTime;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunteerCircleParticipationDetails::class, mappedBy="trnVolunteerCircleEventDetail")
     */
    private $trnVolunteerCircleParticipationDetails;

    public function __construct()
    {
        $this->trnVolunterCircleEventOnSiteAddresses = new ArrayCollection();
        $this->trnCircleEventRecurringDetails = new ArrayCollection();
        $this->trnVolunterCircleEventSubEvents = new ArrayCollection();
        $this->trnVolunterCircleEventVolunterDetails = new ArrayCollection();
        $this->mstSkillSet = new ArrayCollection();
        $this->trnVolunteerCircleParticipationDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkDescription(): ?string
    {
        return $this->workDescription;
    }

    public function setWorkDescription(string $workDescription): self
    {
        $this->workDescription = $workDescription;

        return $this;
    }

    public function getKeyResponsibility(): ?string
    {
        return $this->keyResponsibility;
    }

    public function setKeyResponsibility(string $keyResponsibility): self
    {
        $this->keyResponsibility = $keyResponsibility;

        return $this;
    }

    public function getTargetNumberOfVoluntersRequired(): ?int
    {
        return $this->targetNumberOfVoluntersRequired;
    }

    public function setTargetNumberOfVoluntersRequired(int $targetNumberOfVoluntersRequired): self
    {
        $this->targetNumberOfVoluntersRequired = $targetNumberOfVoluntersRequired;

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

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

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

    public function getMstPlaceOfWork(): ?MstPlaceOfWork
    {
        return $this->mstPlaceOfWork;
    }

    public function setMstPlaceOfWork(?MstPlaceOfWork $mstPlaceOfWork): self
    {
        $this->mstPlaceOfWork = $mstPlaceOfWork;

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

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPincode(): ?string
    {
        return $this->pincode;
    }

    public function setPincode(?string $pincode): self
    {
        $this->pincode = $pincode;

        return $this;
    }

    public function getEventOnSiteMstCity(): ?MstCity
    {
        return $this->eventOnSiteMstCity;
    }

    public function setEventOnSiteMstCity(?MstCity $eventOnSiteMstCity): self
    {
        $this->eventOnSiteMstCity = $eventOnSiteMstCity;

        return $this;
    }

    public function getEventOnSiteLocationLatitude(): ?string
    {
        return $this->eventOnSiteLocationLatitude;
    }

    public function setEventOnSiteLocationLatitude(?string $eventOnSiteLocationLatitude): self
    {
        $this->eventOnSiteLocationLatitude = $eventOnSiteLocationLatitude;

        return $this;
    }

    public function getEventOnSiteLocationLongitude(): ?string
    {
        return $this->eventOnSiteLocationLongitude;
    }

    public function setEventOnSiteLocationLongitude(?string $eventOnSiteLocationLongitude): self
    {
        $this->eventOnSiteLocationLongitude = $eventOnSiteLocationLongitude;

        return $this;
    }

    public function getMstAreasInCity(): ?MstAreasInCity
    {
        return $this->mstAreasInCity;
    }

    public function setMstAreasInCity(?MstAreasInCity $mstAreasInCity): self
    {
        $this->mstAreasInCity = $mstAreasInCity;

        return $this;
    }

    /**
     * @return Collection|TrnVolunterCircleEventOnSiteAddress[]
     */
    public function getTrnVolunterCircleEventOnSiteAddresses(): Collection
    {
        return $this->trnVolunterCircleEventOnSiteAddresses;
    }

    public function addTrnVolunterCircleEventOnSiteAddress(TrnVolunterCircleEventOnSiteAddress $trnVolunterCircleEventOnSiteAddress): self
    {
        if (!$this->trnVolunterCircleEventOnSiteAddresses->contains($trnVolunterCircleEventOnSiteAddress)) {
            $this->trnVolunterCircleEventOnSiteAddresses[] = $trnVolunterCircleEventOnSiteAddress;
            $trnVolunterCircleEventOnSiteAddress->setTrnVolunterCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventOnSiteAddress(TrnVolunterCircleEventOnSiteAddress $trnVolunterCircleEventOnSiteAddress): self
    {
        if ($this->trnVolunterCircleEventOnSiteAddresses->contains($trnVolunterCircleEventOnSiteAddress)) {
            $this->trnVolunterCircleEventOnSiteAddresses->removeElement($trnVolunterCircleEventOnSiteAddress);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventOnSiteAddress->getTrnVolunterCircleEventDetails() === $this) {
                $trnVolunterCircleEventOnSiteAddress->setTrnVolunterCircleEventDetails(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnVolunterCircleEventSubEvents[]
     */
    public function getTrnVolunterCircleEventSubEvents(): Collection
    {
        return $this->trnVolunterCircleEventSubEvents;
    }

    public function addTrnVolunterCircleEventSubEvent(TrnVolunterCircleEventSubEvents $trnVolunterCircleEventSubEvent): self
    {
        if (!$this->trnVolunterCircleEventSubEvents->contains($trnVolunterCircleEventSubEvent)) {
            $this->trnVolunterCircleEventSubEvents[] = $trnVolunterCircleEventSubEvent;
            $trnVolunterCircleEventSubEvent->setTrnVolunterCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventSubEvent(TrnVolunterCircleEventSubEvents $trnVolunterCircleEventSubEvent): self
    {
        if ($this->trnVolunterCircleEventSubEvents->contains($trnVolunterCircleEventSubEvent)) {
            $this->trnVolunterCircleEventSubEvents->removeElement($trnVolunterCircleEventSubEvent);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventSubEvent->getTrnVolunterCircleEventDetails() === $this) {
                $trnVolunterCircleEventSubEvent->setTrnVolunterCircleEventDetails(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnVolunterCircleEventVolunterDetails[]
     */
    public function getTrnVolunterCircleEventVolunterDetails(): Collection
    {
        return $this->trnVolunterCircleEventVolunterDetails;
    }

    public function addTrnVolunterCircleEventVolunterDetail(TrnVolunterCircleEventVolunterDetails $trnVolunterCircleEventVolunterDetail): self
    {
        if (!$this->trnVolunterCircleEventVolunterDetails->contains($trnVolunterCircleEventVolunterDetail)) {
            $this->trnVolunterCircleEventVolunterDetails[] = $trnVolunterCircleEventVolunterDetail;
            $trnVolunterCircleEventVolunterDetail->setTrnVolunterCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventVolunterDetail(TrnVolunterCircleEventVolunterDetails $trnVolunterCircleEventVolunterDetail): self
    {
        if ($this->trnVolunterCircleEventVolunterDetails->contains($trnVolunterCircleEventVolunterDetail)) {
            $this->trnVolunterCircleEventVolunterDetails->removeElement($trnVolunterCircleEventVolunterDetail);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventVolunterDetail->getTrnVolunterCircleEventDetails() === $this) {
                $trnVolunterCircleEventVolunterDetail->setTrnVolunterCircleEventDetails(null);
            }
        }

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

    /**
     * @return Collection|MstSkillSet[]
     */
    public function getMstSkillSet(): Collection
    {
        return $this->mstSkillSet;
    }

    public function addMstSkillSet(MstSkillSet $mstSkillSet): self
    {
        if (!$this->mstSkillSet->contains($mstSkillSet)) {
            $this->mstSkillSet[] = $mstSkillSet;
        }

        return $this;
    }

    public function removeMstSkillSet(MstSkillSet $mstSkillSet): self
    {
        if ($this->mstSkillSet->contains($mstSkillSet)) {
            $this->mstSkillSet->removeElement($mstSkillSet);
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
            $trnCircleEventRecurringDetail->setTrnVolunterCircleEventDetails($this);
        }

        return $this;
    }

    public function removeTrnCircleEventRecurringDetail(TrnCircleEventRecurringDetails $trnCircleEventRecurringDetail): self
    {
        if ($this->trnCircleEventRecurringDetails->contains($trnCircleEventRecurringDetail)) {
            $this->trnCircleEventRecurringDetails->removeElement($trnCircleEventRecurringDetail);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventRecurringDetail->getTrnVolunterCircleEventDetails() === $this) {
                $trnCircleEventRecurringDetail->setTrnVolunterCircleEventDetails(null);
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

    public function setToDate(\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getFromTime(): ?\DateTimeInterface
    {
        return $this->fromTime;
    }

    public function setFromTime(?\DateTimeInterface $fromTime): self
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    public function getToTime(): ?\DateTimeInterface
    {
        return $this->toTime;
    }

    public function setToTime(?\DateTimeInterface $toTime): self
    {
        $this->toTime = $toTime;

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
     * @return Collection|TrnVolunteerCircleParticipationDetails[]
     */
    public function getTrnVolunteerCircleParticipationDetails(): Collection
    {
        return $this->trnVolunteerCircleParticipationDetails;
    }

    public function addTrnVolunteerParticipationDetail(TrnVolunteerCircleParticipationDetails $trnVolunteerParticipationDetail): self
    {
        if (!$this->trnVolunteerCircleParticipationDetails->contains($trnVolunteerParticipationDetail)) {
            $this->trnVolunteerCircleParticipationDetails[] = $trnVolunteerParticipationDetail;
            $trnVolunteerParticipationDetail->setTrnVolunteerCircleEventDetail($this);
        }

        return $this;
    }

    public function removeTrnVolunteerParticipationDetail(TrnVolunteerCircleParticipationDetails $trnVolunteerParticipationDetail): self
    {
        if ($this->trnVolunteerCircleParticipationDetails->removeElement($trnVolunteerParticipationDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnVolunteerParticipationDetail->getTrnVolunteerCircleEventDetail() === $this) {
                $trnVolunteerParticipationDetail->setTrnVolunteerCircleEventDetail(null);
            }
        }

        return $this;
    }
}
