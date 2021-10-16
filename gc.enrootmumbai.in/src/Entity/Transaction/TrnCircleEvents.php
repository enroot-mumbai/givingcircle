<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Service\FileUploaderHelper;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventsRepository::class)
 * @ORM\Table("trncircleevents")
 * @UniqueEntity(fields={"name"}, message="The value is already in the system")
 */
class TrnCircleEvents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircle;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $eventPurpose;

    /**
     * @ORM\ManyToOne(targetEntity=MstJoinBy::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstJoinBy;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="text")
     */
    private $highlightsOfEvent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eventLink;

    /**
     * @ORM\Column(type="string", length=48)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

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
    private $isUrgent;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventUploadedDocuments::class, cascade={"persist", "remove"}, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnCircleEventUploadedDocuments;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventInvitations::class, mappedBy="trnCircleEvent", orphanRemoval=true)
     */
    private $trnCircleEventInvitations;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventRequestToParticipate::class, mappedBy="trnCircleEvent", orphanRemoval=true)
     */
    private $trnCircleEventRequestToParticipates;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventDetails::class,cascade={"persist", "remove"}, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnFundRaiserCircleEventDetails::class,cascade={"persist", "remove"}, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnFundRaiserCircleEventDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventRecurringDetails::class, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnCircleEventRecurringDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialInKindCircleEventDetails::class,cascade={"persist", "remove"}, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnMaterialInKindCircleEventDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialReceivedAtCollectionCentre::class, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnMaterialReceivedAtCollectionCentres;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventVolunterDetails::class, mappedBy="trnCircleEvents", orphanRemoval=true)
     */
    private $trnVolunterCircleEventVolunterDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventBroadCastDetails::class, mappedBy="trnCircleEvent", orphanRemoval=true)
     */
    private $trnCircleEventBroadCastDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventGoodnessTimeline::class, mappedBy="trnCircleEvent", orphanRemoval=true)
     */
    private $trnCircleEventGoodnessTimelines;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCountry;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstStatus;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToMany(targetEntity=MstEventProductType::class)
     */
    private $mstEventProductType;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="childTrnCircleEvents")
     */
    private $parentTrnCircleEvents;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEvents::class, mappedBy="parentTrnCircleEvents")
     */
    private $childTrnCircleEvents;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAffiliated;

    /**
     * @ORM\OneToMany(targetEntity=TrnProductMedia::class, cascade={"persist", "remove"},mappedBy="trnCircleEvents")
     */
    private $trnProductMedia;

    /**
     * @ORM\OneToMany(targetEntity=TrnAreaOfInterest::class, cascade={"persist", "remove"}, mappedBy="trnCircleEvents")
     */
    private $trnAreaOfInterests;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $toDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $readCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likeCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shareCount;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventComments::class, mappedBy="trnCircleEvents")
     */
    private $trnCircleEventComments;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCrowdFunding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImagePath;

    /**
     * @ORM\OneToMany(targetEntity=TrnCrowdFundEvent::class, cascade={"persist", "remove"}, mappedBy="trnCircleEvent")
     */
    private $trnCrowdFundEvents;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrder::class, mappedBy="trnCircleEvent")
     */
    private $trnOrders;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrderDetail::class, mappedBy="trnCircleEvent")
     */
    private $trnOrderDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunteerCircleParticipationDetails::class, mappedBy="trnCircleEvent")
     */
    private $trnVolunteerCircleParticipationDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventLeads::class, mappedBy="trnCircleEvents")
     */
    private $trnCircleEventLeads;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasonToReject;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventDeactivatingReason::class, mappedBy="trnCircleEvents")
     */
    private $trnCircleEventDeactivatingReasons;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventReminder::class, mappedBy="trnCircleEvents")
     */
    private $trnCircleEventReminders;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTargetAchieved;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventsVisitors::class, mappedBy="trnCircleEvents")
     */
    private $trnCircleEventsVisitors;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTrending;

    public function __construct()
    {
        $this->trnCircleEventUploadedDocuments = new ArrayCollection();
        $this->trnCircleEventInvitations = new ArrayCollection();
        $this->trnCircleEventRequestToParticipates = new ArrayCollection();
        $this->trnVolunterCircleEventDetails = new ArrayCollection();
        $this->trnFundRaiserCircleEventDetails = new ArrayCollection();
        $this->trnCircleEventRecurringDetails = new ArrayCollection();
        $this->trnMaterialInKindCircleEventDetails = new ArrayCollection();
        $this->trnMaterialReceivedAtCollectionCentres = new ArrayCollection();
        $this->trnVolunterCircleEventVolunterDetails = new ArrayCollection();
        $this->trnCircleEventBroadCastDetails = new ArrayCollection();
        $this->trnCircleEventGoodnessTimelines = new ArrayCollection();
        $this->mstEventProductType = new ArrayCollection();
        $this->childTrnCircleEvents = new ArrayCollection();
        $this->trnProductMedia = new ArrayCollection();
        $this->trnAreaOfInterests = new ArrayCollection();
        $this->trnCircleEventComments = new ArrayCollection();
        $this->trnCrowdFundEvents = new ArrayCollection();
        $this->trnOrders = new ArrayCollection();
        $this->trnOrderDetails = new ArrayCollection();
        $this->trnVolunteerCircleParticipationDetails = new ArrayCollection();
        $this->trnCircleEventLeads = new ArrayCollection();
        $this->trnCircleEventDeactivatingReasons = new ArrayCollection();
        $this->trnCircleEventReminders = new ArrayCollection();
        $this->trnCircleEventsVisitors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnCircle(): ?TrnCircle
    {
        return $this->trnCircle;
    }

    public function setTrnCircle(?TrnCircle $trnCircle): self
    {
        $this->trnCircle = $trnCircle;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEventPurpose(): ?string
    {
        return $this->eventPurpose;
    }

    public function setEventPurpose(string $eventPurpose): self
    {
        $this->eventPurpose = $eventPurpose;

        return $this;
    }

    public function getMstJoinBy(): ?MstJoinBy
    {
        return $this->mstJoinBy;
    }

    public function setMstJoinBy(?MstJoinBy $mstJoinBy): self
    {
        $this->mstJoinBy = $mstJoinBy;

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

    public function getHighlightsOfEvent(): ?string
    {
        return $this->highlightsOfEvent;
    }

    public function setHighlightsOfEvent(string $highlightsOfEvent): self
    {
        $this->highlightsOfEvent = $highlightsOfEvent;

        return $this;
    }

    public function getEventLink(): ?string
    {
        return $this->eventLink;
    }

    public function setEventLink(?string $eventLink): self
    {
        $this->eventLink = $eventLink;

        return $this;
    }

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }

    public function getLocationLatitude(): ?string
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(string $locationLatitude): self
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(string $locationLongitude): self
    {
        $this->locationLongitude = $locationLongitude;

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

    public function getOrgCompany(): ?OrgCompany
    {
        return $this->orgCompany;
    }

    public function setOrgCompany(?OrgCompany $orgCompany): self
    {
        $this->orgCompany = $orgCompany;

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
     * @return Collection|TrnCircleEventUploadedDocuments[]
     */
    public function getTrnCircleEventUploadedDocuments(): Collection
    {
        return $this->trnCircleEventUploadedDocuments;
    }

    public function addTrnCircleEventUploadedDocument(TrnCircleEventUploadedDocuments $trnCircleEventUploadedDocument): self
    {
        if (!$this->trnCircleEventUploadedDocuments->contains($trnCircleEventUploadedDocument)) {
            $this->trnCircleEventUploadedDocuments[] = $trnCircleEventUploadedDocument;
            $trnCircleEventUploadedDocument->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventUploadedDocument(TrnCircleEventUploadedDocuments $trnCircleEventUploadedDocument): self
    {
        if ($this->trnCircleEventUploadedDocuments->contains($trnCircleEventUploadedDocument)) {
            $this->trnCircleEventUploadedDocuments->removeElement($trnCircleEventUploadedDocument);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventUploadedDocument->getTrnCircleEvents() === $this) {
                $trnCircleEventUploadedDocument->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventInvitations[]
     */
    public function getTrnCircleEventInvitations(): Collection
    {
        return $this->trnCircleEventInvitations;
    }

    public function addTrnCircleEventInvitation(TrnCircleEventInvitations $trnCircleEventInvitation): self
    {
        if (!$this->trnCircleEventInvitations->contains($trnCircleEventInvitation)) {
            $this->trnCircleEventInvitations[] = $trnCircleEventInvitation;
            $trnCircleEventInvitation->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnCircleEventInvitation(TrnCircleEventInvitations $trnCircleEventInvitation): self
    {
        if ($this->trnCircleEventInvitations->contains($trnCircleEventInvitation)) {
            $this->trnCircleEventInvitations->removeElement($trnCircleEventInvitation);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventInvitation->getTrnCircleEvent() === $this) {
                $trnCircleEventInvitation->setTrnCircleEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventRequestToParticipate[]
     */
    public function getTrnCircleEventRequestToParticipates(): Collection
    {
        return $this->trnCircleEventRequestToParticipates;
    }

    public function addTrnCircleEventRequestToParticipate(TrnCircleEventRequestToParticipate $trnCircleEventRequestToParticipate): self
    {
        if (!$this->trnCircleEventRequestToParticipates->contains($trnCircleEventRequestToParticipate)) {
            $this->trnCircleEventRequestToParticipates[] = $trnCircleEventRequestToParticipate;
            $trnCircleEventRequestToParticipate->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnCircleEventRequestToParticipate(TrnCircleEventRequestToParticipate $trnCircleEventRequestToParticipate): self
    {
        if ($this->trnCircleEventRequestToParticipates->contains($trnCircleEventRequestToParticipate)) {
            $this->trnCircleEventRequestToParticipates->removeElement($trnCircleEventRequestToParticipate);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventRequestToParticipate->getTrnCircleEvent() === $this) {
                $trnCircleEventRequestToParticipate->setTrnCircleEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnVolunterCircleEventDetails[]
     */
    public function getTrnVolunterCircleEventDetails(): Collection
    {
        return $this->trnVolunterCircleEventDetails;
    }

    public function addTrnVolunterCircleEventDetail(TrnVolunterCircleEventDetails $trnVolunterCircleEventDetail): self
    {
        if (!$this->trnVolunterCircleEventDetails->contains($trnVolunterCircleEventDetail)) {
            $this->trnVolunterCircleEventDetails[] = $trnVolunterCircleEventDetail;
            $trnVolunterCircleEventDetail->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventDetail(TrnVolunterCircleEventDetails $trnVolunterCircleEventDetail): self
    {
        if ($this->trnVolunterCircleEventDetails->contains($trnVolunterCircleEventDetail)) {
            $this->trnVolunterCircleEventDetails->removeElement($trnVolunterCircleEventDetail);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventDetail->getTrnCircleEvents() === $this) {
                $trnVolunterCircleEventDetail->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnFundRaiserCircleEventDetails[]
     */
    public function getTrnFundRaiserCircleEventDetails(): Collection
    {
        return $this->trnFundRaiserCircleEventDetails;
    }

    public function addTrnFundRaiserCircleEventDetail(TrnFundRaiserCircleEventDetails $trnFundRaiserCircleEventDetail): self
    {
        if (!$this->trnFundRaiserCircleEventDetails->contains($trnFundRaiserCircleEventDetail)) {
            $this->trnFundRaiserCircleEventDetails[] = $trnFundRaiserCircleEventDetail;
            $trnFundRaiserCircleEventDetail->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnFundRaiserCircleEventDetail(TrnFundRaiserCircleEventDetails $trnFundRaiserCircleEventDetail): self
    {
        if ($this->trnFundRaiserCircleEventDetails->contains($trnFundRaiserCircleEventDetail)) {
            $this->trnFundRaiserCircleEventDetails->removeElement($trnFundRaiserCircleEventDetail);
            // set the owning side to null (unless already changed)
            if ($trnFundRaiserCircleEventDetail->getTrnCircleEvents() === $this) {
                $trnFundRaiserCircleEventDetail->setTrnCircleEvents(null);
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
            $trnCircleEventRecurringDetail->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventRecurringDetail(TrnCircleEventRecurringDetails $trnCircleEventRecurringDetail): self
    {
        if ($this->trnCircleEventRecurringDetails->contains($trnCircleEventRecurringDetail)) {
            $this->trnCircleEventRecurringDetails->removeElement($trnCircleEventRecurringDetail);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventRecurringDetail->getTrnCircleEvents() === $this) {
                $trnCircleEventRecurringDetail->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnMaterialInKindCircleEventDetails[]
     */
    public function getTrnMaterialInKindCircleEventDetails(): Collection
    {
        return $this->trnMaterialInKindCircleEventDetails;
    }

    public function addTrnMaterialInKindCircleEventDetails(TrnMaterialInKindCircleEventDetails $trnMaterialInKindCircleEventDetails): self
    {
        if (!$this->trnMaterialInKindCircleEventDetails->contains($trnMaterialInKindCircleEventDetails)) {
            $this->trnMaterialInKindCircleEventDetails[] = $trnMaterialInKindCircleEventDetails;
            $trnMaterialInKindCircleEventDetails->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnMaterialInKindCircleEventDetails(TrnMaterialInKindCircleEventDetails $trnMaterialInKindCircleEventDetails): self
    {
        if ($this->trnMaterialInKindCircleEventDetails->contains($trnMaterialInKindCircleEventDetails)) {
            $this->trnMaterialInKindCircleEventDetails->removeElement($trnMaterialInKindCircleEventDetails);
            // set the owning side to null (unless already changed)
            if ($trnMaterialInKindCircleEventDetails->getTrnCircleEvents() === $this) {
                $trnMaterialInKindCircleEventDetails->setTrnCircleEvents(null);
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
            $trnMaterialReceivedAtCollectionCentre->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnMaterialReceivedAtCollectionCentre(TrnMaterialReceivedAtCollectionCentre $trnMaterialReceivedAtCollectionCentre): self
    {
        if ($this->trnMaterialReceivedAtCollectionCentres->contains($trnMaterialReceivedAtCollectionCentre)) {
            $this->trnMaterialReceivedAtCollectionCentres->removeElement($trnMaterialReceivedAtCollectionCentre);
            // set the owning side to null (unless already changed)
            if ($trnMaterialReceivedAtCollectionCentre->getTrnCircleEvents() === $this) {
                $trnMaterialReceivedAtCollectionCentre->setTrnCircleEvents(null);
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
            $trnVolunterCircleEventVolunterDetail->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventVolunterDetail(TrnVolunterCircleEventVolunterDetails $trnVolunterCircleEventVolunterDetail): self
    {
        if ($this->trnVolunterCircleEventVolunterDetails->contains($trnVolunterCircleEventVolunterDetail)) {
            $this->trnVolunterCircleEventVolunterDetails->removeElement($trnVolunterCircleEventVolunterDetail);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventVolunterDetail->getTrnCircleEvents() === $this) {
                $trnVolunterCircleEventVolunterDetail->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventBroadCastDetails[]
     */
    public function getTrnCircleEventBroadCastDetails(): Collection
    {
        return $this->trnCircleEventBroadCastDetails;
    }

    public function addTrnCircleEventBroadCastDetail(TrnCircleEventBroadCastDetails $trnCircleEventBroadCastDetail): self
    {
        if (!$this->trnCircleEventBroadCastDetails->contains($trnCircleEventBroadCastDetail)) {
            $this->trnCircleEventBroadCastDetails[] = $trnCircleEventBroadCastDetail;
            $trnCircleEventBroadCastDetail->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnCircleEventBroadCastDetail(TrnCircleEventBroadCastDetails $trnCircleEventBroadCastDetail): self
    {
        if ($this->trnCircleEventBroadCastDetails->contains($trnCircleEventBroadCastDetail)) {
            $this->trnCircleEventBroadCastDetails->removeElement($trnCircleEventBroadCastDetail);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventBroadCastDetail->getTrnCircleEvent() === $this) {
                $trnCircleEventBroadCastDetail->setTrnCircleEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventGoodnessTimeline[]
     */
    public function getTrnCircleEventGoodnessTimelines(): Collection
    {
        return $this->trnCircleEventGoodnessTimelines;
    }

    public function addTrnCircleEventGoodnessTimeline(TrnCircleEventGoodnessTimeline $trnCircleEventGoodnessTimeline): self
    {
        if (!$this->trnCircleEventGoodnessTimelines->contains($trnCircleEventGoodnessTimeline)) {
            $this->trnCircleEventGoodnessTimelines[] = $trnCircleEventGoodnessTimeline;
            $trnCircleEventGoodnessTimeline->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnCircleEventGoodnessTimeline(TrnCircleEventGoodnessTimeline $trnCircleEventGoodnessTimeline): self
    {
        if ($this->trnCircleEventGoodnessTimelines->contains($trnCircleEventGoodnessTimeline)) {
            $this->trnCircleEventGoodnessTimelines->removeElement($trnCircleEventGoodnessTimeline);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventGoodnessTimeline->getTrnCircleEvent() === $this) {
                $trnCircleEventGoodnessTimeline->setTrnCircleEvent(null);
            }
        }

        return $this;
    }

    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }

    public function getMstState(): ?MstState
    {
        return $this->mstState;
    }

    public function setMstState(?MstState $mstState): self
    {
        $this->mstState = $mstState;

        return $this;
    }

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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

    public  function getAddress()
    {
        return $this->mstCity->getCity(). ', '.$this->mstState->getState(). ', '.$this->mstCountry->getCountry();
    }

    public  function getGeoCode()
    {
        return ' Latitude :- '.$this->locationLatitude. ', Longitude:- '.$this->locationLongitude;
    }

    /**
     * @return Collection|MstEventProductType[]
     */
    public function getMstEventProductType(): Collection
    {
        return $this->mstEventProductType;
    }

    public function addMstEventProductType(MstEventProductType $mstEventProductType): self
    {
        if (!$this->mstEventProductType->contains($mstEventProductType)) {
            $this->mstEventProductType[] = $mstEventProductType;
        }

        return $this;
    }

    public function removeMstEventProductType(MstEventProductType $mstEventProductType): self
    {
        if ($this->mstEventProductType->contains($mstEventProductType)) {
            $this->mstEventProductType->removeElement($mstEventProductType);
        }

        return $this;
    }

    public  function getIsDistributedEvent(){
        if (!empty($this->trnFundRaiserCircleEventDetails) && !empty($this->trnFundRaiserCircleEventDetails[0])) {
            $trnFundRaiserCircleEventDetails = $this->trnFundRaiserCircleEventDetails[0];
            return $trnFundRaiserCircleEventDetails->getIsDistributedEvent();
        }
        return false;
    }

    public function getParentTrnCircleEvents(): ?self
    {
        return $this->parentTrnCircleEvents;
    }

    public function setParentTrnCircleEvents(?self $parentTrnCircleEvents): self
    {
        $this->parentTrnCircleEvents = $parentTrnCircleEvents;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildTrnCircleEvents(): Collection
    {
        return $this->childTrnCircleEvents;
    }

    public function addChildTrnCircleEvent(self $childTrnCircleEvent): self
    {
        if (!$this->childTrnCircleEvents->contains($childTrnCircleEvent)) {
            $this->childTrnCircleEvents[] = $childTrnCircleEvent;
            $childTrnCircleEvent->setParentTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeChildTrnCircleEvent(self $childTrnCircleEvent): self
    {
        if ($this->childTrnCircleEvents->contains($childTrnCircleEvent)) {
            $this->childTrnCircleEvents->removeElement($childTrnCircleEvent);
            // set the owning side to null (unless already changed)
            if ($childTrnCircleEvent->getParentTrnCircleEvents() === $this) {
                $childTrnCircleEvent->setParentTrnCircleEvents(null);
            }
        }

        return $this;
    }

    public function getIsAffiliated(): ?bool
    {
        return $this->isAffiliated;
    }

    public function setIsAffiliated(?bool $isAffiliated): self
    {
        $this->isAffiliated = $isAffiliated;

        return $this;
    }

    /**
     * @return Collection|TrnProductMedia[]
     */
    public function getTrnProductMedia(): Collection
    {
        return $this->trnProductMedia;
    }

    public function addTrnProductMedium(TrnProductMedia $trnProductMedium): self
    {
        if (!$this->trnProductMedia->contains($trnProductMedium)) {
            $this->trnProductMedia[] = $trnProductMedium;
            $trnProductMedium->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnProductMedium(TrnProductMedia $trnProductMedium): self
    {
        if ($this->trnProductMedia->contains($trnProductMedium)) {
            $this->trnProductMedia->removeElement($trnProductMedium);
            // set the owning side to null (unless already changed)
            if ($trnProductMedium->getTrnCircleEvents() === $this) {
                $trnProductMedium->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnAreaOfInterest[]
     */
    public function getTrnAreaOfInterests(): Collection
    {
        return $this->trnAreaOfInterests;
    }

    public function addTrnAreaOfInterest(TrnAreaOfInterest $trnAreaOfInterest): self
    {
        if (!$this->trnAreaOfInterests->contains($trnAreaOfInterest)) {
            $this->trnAreaOfInterests[] = $trnAreaOfInterest;
            $trnAreaOfInterest->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnAreaOfInterest(TrnAreaOfInterest $trnAreaOfInterest): self
    {
        if ($this->trnAreaOfInterests->contains($trnAreaOfInterest)) {
            $this->trnAreaOfInterests->removeElement($trnAreaOfInterest);
            // set the owning side to null (unless already changed)
            if ($trnAreaOfInterest->getTrnCircleEvents() === $this) {
                $trnAreaOfInterest->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): self
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

    public function getReadCount(): ?int
    {
        return $this->readCount;
    }

    public function setReadCount(?int $readCount): self
    {
        $this->readCount = $readCount;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    public function getShareCount(): ?int
    {
        return $this->shareCount;
    }

    public function setShareCount(?int $shareCount): self
    {
        $this->shareCount = $shareCount;

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventComments[]
     */
    public function getTrnCircleEventComments(): Collection
    {
        return $this->trnCircleEventComments;
    }

    public function addTrnCircleEventComment(TrnCircleEventComments $trnCircleEventComment): self
    {
        if (!$this->trnCircleEventComments->contains($trnCircleEventComment)) {
            $this->trnCircleEventComments[] = $trnCircleEventComment;
            $trnCircleEventComment->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventComment(TrnCircleEventComments $trnCircleEventComment): self
    {
        if ($this->trnCircleEventComments->contains($trnCircleEventComment)) {
            $this->trnCircleEventComments->removeElement($trnCircleEventComment);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventComment->getTrnCircleEvents() === $this) {
                $trnCircleEventComment->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    public function getIsCrowdFunding(): ?bool
    {
        return $this->isCrowdFunding;
    }

    public function setIsCrowdFunding(?bool $isCrowdFunding): self
    {
        $this->isCrowdFunding = $isCrowdFunding;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getBackgroundImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->backgroundImagePath;
    }

    public function setBackgroundImagePath(?string $backgroundImagePath): self
    {
        $this->backgroundImagePath = $backgroundImagePath;

        return $this;
    }

    /**
     * @return Collection|TrnCrowdFundEvent[]
     */
    public function getTrnCrowdFundEvents(): Collection
    {
        return $this->trnCrowdFundEvents;
    }

    public function addTrnCrowdFundEvent(TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        if (!$this->trnCrowdFundEvents->contains($trnCrowdFundEvent)) {
            $this->trnCrowdFundEvents[] = $trnCrowdFundEvent;
            $trnCrowdFundEvent->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnCrowdFundEvent(TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        if ($this->trnCrowdFundEvents->removeElement($trnCrowdFundEvent)) {
            // set the owning side to null (unless already changed)
            if ($trnCrowdFundEvent->getTrnCircleEvent() === $this) {
                $trnCrowdFundEvent->setTrnCircleEvent(null);
            }
        }

        return $this;
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
            $trnOrder->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnOrder(TrnOrder $trnOrder): self
    {
        if ($this->trnOrders->removeElement($trnOrder)) {
            // set the owning side to null (unless already changed)
            if ($trnOrder->getTrnCircleEvent() === $this) {
                $trnOrder->setTrnCircleEvent(null);
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
            $trnOrderDetail->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if ($this->trnOrderDetails->removeElement($trnOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnOrderDetail->getTrnCircleEvent() === $this) {
                $trnOrderDetail->setTrnCircleEvent(null);
            }
        }

        return $this;
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
            $trnVolunteerParticipationDetail->setTrnCircleEvent($this);
        }

        return $this;
    }

    public function removeTrnVolunteerParticipationDetail(TrnVolunteerCircleParticipationDetails $trnVolunteerParticipationDetail): self
    {
        if ($this->trnVolunteerCircleParticipationDetails->removeElement($trnVolunteerParticipationDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnVolunteerParticipationDetail->getTrnCircleEvent() === $this) {
                $trnVolunteerParticipationDetail->setTrnCircleEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventLeads[]
     */
    public function getTrnCircleEventLeads(): Collection
    {
        return $this->trnCircleEventLeads;
    }

    public function addTrnCircleEventLead(TrnCircleEventLeads $trnCircleEventLead): self
    {
        if (!$this->trnCircleEventLeads->contains($trnCircleEventLead)) {
            $this->trnCircleEventLeads[] = $trnCircleEventLead;
            $trnCircleEventLead->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventLead(TrnCircleEventLeads $trnCircleEventLead): self
    {
        if ($this->trnCircleEventLeads->removeElement($trnCircleEventLead)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventLead->getTrnCircleEvents() === $this) {
                $trnCircleEventLead->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    public function getReasonToReject(): ?string
    {
        return $this->reasonToReject;
    }

    public function setReasonToReject(?string $reasonToReject): self
    {
        $this->reasonToReject = $reasonToReject;

        return $this;
    }

    public function getIsTargetAchieved(): ?bool
    {
        return $this->isTargetAchieved;
    }

    public function setIsTargetAchieved(?bool $isTargetAchieved): self
    {
        $this->isTargetAchieved = $isTargetAchieved;

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventDeactivatingReason[]
     */
    public function getTrnCircleEventDeactivatingReasons(): Collection
    {
        return $this->trnCircleEventDeactivatingReasons;
    }

    public function addTrnCircleEventDeactivatingReason(TrnCircleEventDeactivatingReason $trnCircleEventDeactivatingReason): self
    {
        if (!$this->trnCircleEventDeactivatingReasons->contains($trnCircleEventDeactivatingReason)) {
            $this->trnCircleEventDeactivatingReasons[] = $trnCircleEventDeactivatingReason;
            $trnCircleEventDeactivatingReason->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventDeactivatingReason(TrnCircleEventDeactivatingReason $trnCircleEventDeactivatingReason): self
    {
        if ($this->trnCircleEventDeactivatingReasons->removeElement($trnCircleEventDeactivatingReason)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventDeactivatingReason->getTrnCircleEvents() === $this) {
                $trnCircleEventDeactivatingReason->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventReminder[]
     */
    public function getTrnCircleEventReminders(): Collection
    {
        return $this->trnCircleEventReminders;
    }

    public function addTrnCircleEventReminder(TrnCircleEventReminder $trnCircleEventReminder): self
    {
        if (!$this->trnCircleEventReminders->contains($trnCircleEventReminder)) {
            $this->trnCircleEventReminders[] = $trnCircleEventReminder;
            $trnCircleEventReminder->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventReminder(TrnCircleEventReminder $trnCircleEventReminder): self
    {
        if ($this->trnCircleEventReminders->removeElement($trnCircleEventReminder)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventReminder->getTrnCircleEvents() === $this) {
                $trnCircleEventReminder->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCircleEventsVisitors[]
     */
    public function getTrnCircleEventsVisitors(): Collection
    {
        return $this->trnCircleEventsVisitors;
    }

    public function addTrnCircleEventsVisitor(TrnCircleEventsVisitors $trnCircleEventsVisitor): self
    {
        if (!$this->trnCircleEventsVisitors->contains($trnCircleEventsVisitor)) {
            $this->trnCircleEventsVisitors[] = $trnCircleEventsVisitor;
            $trnCircleEventsVisitor->setTrnCircleEvents($this);
        }

        return $this;
    }

    public function removeTrnCircleEventsVisitor(TrnCircleEventsVisitors $trnCircleEventsVisitor): self
    {
        if ($this->trnCircleEventsVisitors->removeElement($trnCircleEventsVisitor)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventsVisitor->getTrnCircleEvents() === $this) {
                $trnCircleEventsVisitor->setTrnCircleEvents(null);
            }
        }

        return $this;
    }

    public function getIsTrending(): ?bool
    {
        return $this->isTrending;
    }

    public function setIsTrending(?bool $isTrending): self
    {
        $this->isTrending = $isTrending;

        return $this;
    }
}
