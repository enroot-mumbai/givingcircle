<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstHighlights;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleRepository;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TrnCircleRepository::class)
 * @ORM\Table("trncircle")
 * @UniqueEntity(fields={"circle"}, message="The value is already in the system")
 */
class TrnCircle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $circle;

    /**
     * @ORM\Column(type="string", length=48)
     */
    private $userIpAddress;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstStatus;

    /**
     * @ORM\ManyToOne(targetEntity=MstJoinBy::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstJoinBy;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleUploadDocuments::class, cascade={"persist", "remove"}, mappedBy="trnCircle", orphanRemoval=true)
     */
    private $trnCircleUploadDocuments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

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
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $impactStatement;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $howGoalWillBeAchieved;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $circleInformation;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleInvitations::class, mappedBy="trnCircle", orphanRemoval=true)
     */
    private $trnCircleInvitations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $circleLink;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleRequestToJoin::class, mappedBy="trnCircle", orphanRemoval=true)
     */
    private $trnCircleRequestToJoins;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEvents::class, mappedBy="trnCircle", orphanRemoval=true)
     */
    private $trnCircleEvents;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCountry;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $beneficiaryBankName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $beneficiaryAccountHolderName;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $beneficiaryBankAccountNumber;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $beneficiaryIfscCode;

    /**
     * @ORM\ManyToOne(targetEntity=MstBankAccountType::class)
     */
    private $mstBankAccountTypeBeneficiary;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAffiliated;

    /**
     * @ORM\OneToMany(targetEntity=TrnProductMedia::class, mappedBy="trnCircle")
     */
    private $trnProductMedia;

    /**
     * @ORM\OneToMany(targetEntity=TrnCollectionCentreDetails::class, mappedBy="trnCircle")
     */
    private $trnCollectionCentreDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnAreaOfInterest::class, cascade={"persist", "remove"}, mappedBy="trnCircle")
     */
    private $trnAreaOfInterests;

    /**
     * @ORM\ManyToOne(targetEntity=MstHighlights::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstHighlights;

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
     * @ORM\OneToMany(targetEntity=TrnCircleEventComments::class, mappedBy="trnCircle")
     */
    private $trnCircleEventComments;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $suggestedKeywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profileImagePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundImagePath;

    /**
     * @ORM\OneToMany(targetEntity=TrnCrowdFundEvent::class, mappedBy="trnCircle")
     */
    private $trnCrowdFundEvents;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $reasonToReject;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventBroadCastDetails::class, mappedBy="trnCircle")
     */
    private $trnCircleEventBroadCastDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventLeads::class, mappedBy="trnCircle")
     */
    private $trnCircleEventLeads;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventsVisitors::class, mappedBy="trnCircle")
     */
    private $trnCircleEventsVisitors;

    public function __construct()
    {
        $this->trnCircleUploadDocuments = new ArrayCollection();
        $this->trnCircleInvitations = new ArrayCollection();
        $this->trnCircleRequestToJoins = new ArrayCollection();
        $this->trnCircleEvents = new ArrayCollection();
        $this->trnProductMedia = new ArrayCollection();
        $this->trnCollectionCentreDetails = new ArrayCollection();
        $this->trnAreaOfInterests = new ArrayCollection();
        $this->trnCircleEventComments = new ArrayCollection();
        $this->trnCrowdFundEvents = new ArrayCollection();
        $this->trnCircleEventBroadCastDetails = new ArrayCollection();
        $this->trnCircleEventLeads = new ArrayCollection();
        $this->trnCircleEventsVisitors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCircle(): ?string
    {
        return $this->circle;
    }

    public function setCircle(string $circle): self
    {
        $this->circle = $circle;

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

    public function getMstCity(): ?MstCity
    {
        return $this->mstCity;
    }

    public function setMstCity(?MstCity $mstCity): self
    {
        $this->mstCity = $mstCity;

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

    public function getMstJoinBy(): ?MstJoinBy
    {
        return $this->mstJoinBy;
    }

    public function setMstJoinBy(?MstJoinBy $mstJoinBy): self
    {
        $this->mstJoinBy = $mstJoinBy;

        return $this;
    }

    public function getMstHighlights(): ?MstHighlights
    {
        return $this->mstHighlights;
    }

    public function setMstHighlights(?MstHighlights $mstHighlights): self
    {
        $this->mstHighlights = $mstHighlights;

        return $this;
    }

    /**
     * @return Collection|TrnCircleUploadDocuments[]
     */
    public function getTrnCircleUploadDocuments(): Collection
    {
        return $this->trnCircleUploadDocuments;
    }

    public function addTrnCircleUploadDocument(TrnCircleUploadDocuments $trnCircleUploadDocument): self
    {
        if (!$this->trnCircleUploadDocuments->contains($trnCircleUploadDocument)) {
            $this->trnCircleUploadDocuments[] = $trnCircleUploadDocument;
            $trnCircleUploadDocument->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleUploadDocument(TrnCircleUploadDocuments $trnCircleUploadDocument): self
    {
        if ($this->trnCircleUploadDocuments->contains($trnCircleUploadDocument)) {
            $this->trnCircleUploadDocuments->removeElement($trnCircleUploadDocument);
            // set the owning side to null (unless already changed)
            if ($trnCircleUploadDocument->getTrnCircle() === $this) {
                $trnCircleUploadDocument->setTrnCircle(null);
            }
        }

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

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): self
    {
        $this->appUser = $appUser;

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

    public function getImpactStatement(): ?string
    {
        return $this->impactStatement;
    }

    public function setImpactStatement(string $impactStatement): self
    {
        $this->impactStatement = $impactStatement;

        return $this;
    }

    public function getHowGoalWillBeAchieved(): ?string
    {
        return $this->howGoalWillBeAchieved;
    }

    public function setHowGoalWillBeAchieved(string $howGoalWillBeAchieved): self
    {
        $this->howGoalWillBeAchieved = $howGoalWillBeAchieved;

        return $this;
    }

    public function getCircleInformation(): ?string
    {
        return $this->circleInformation;
    }

    public function setCircleInformation(string $circleInformation): self
    {
        $this->circleInformation = $circleInformation;

        return $this;
    }

    /**
     * @return Collection|TrnCircleInvitations[]
     */
    public function getTrnCircleInvitations(): Collection
    {
        return $this->trnCircleInvitations;
    }

    public function addTrnCircleInvitations(TrnCircleInvitations $trnCircleInvitations): self
    {
        if (!$this->trnCircleInvitations->contains($trnCircleInvitations)) {
            $this->trnCircleInvitations[] = $trnCircleInvitations;
            $trnCircleInvitations->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleInvitations(TrnCircleInvitations $trnCircleInvitations): self
    {
        if ($this->trnCircleInvitations->contains($trnCircleInvitations)) {
            $this->trnCircleInvitations->removeElement($trnCircleInvitations);
            // set the owning side to null (unless already changed)
            if ($trnCircleInvitations->getTrnCircle() === $this) {
                $trnCircleInvitations->setTrnCircle(null);
            }
        }

        return $this;
    }

    public function getCircleLink(): ?string
    {
        return $this->circleLink;
    }

    public function setCircleLink(?string $circleLink): self
    {
        $this->circleLink = $circleLink;

        return $this;
    }

    /**
     * @return Collection|TrnCircleRequestToJoin[]
     */
    public function getTrnCircleRequestToJoins(): Collection
    {
        return $this->trnCircleRequestToJoins;
    }

    public function addTrnCircleRequestToJoin(TrnCircleRequestToJoin $trnCircleRequestToJoin): self
    {
        if (!$this->trnCircleRequestToJoins->contains($trnCircleRequestToJoin)) {
            $this->trnCircleRequestToJoins[] = $trnCircleRequestToJoin;
            $trnCircleRequestToJoin->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleRequestToJoin(TrnCircleRequestToJoin $trnCircleRequestToJoin): self
    {
        if ($this->trnCircleRequestToJoins->contains($trnCircleRequestToJoin)) {
            $this->trnCircleRequestToJoins->removeElement($trnCircleRequestToJoin);
            // set the owning side to null (unless already changed)
            if ($trnCircleRequestToJoin->getTrnCircle() === $this) {
                $trnCircleRequestToJoin->setTrnCircle(null);
            }
        }

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

    /**
     * @return Collection|TrnCircleEvents[]
     */
    public function getTrnCircleEvents(): Collection
    {
        return $this->trnCircleEvents;
    }

    public function addTrnCircleEvent(TrnCircleEvents $trnCircleEvent): self
    {
        if (!$this->trnCircleEvents->contains($trnCircleEvent)) {
            $this->trnCircleEvents[] = $trnCircleEvent;
            $trnCircleEvent->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleEvent(TrnCircleEvents $trnCircleEvent): self
    {
        if ($this->trnCircleEvents->contains($trnCircleEvent)) {
            $this->trnCircleEvents->removeElement($trnCircleEvent);
            // set the owning side to null (unless already changed)
            if ($trnCircleEvent->getTrnCircle() === $this) {
                $trnCircleEvent->setTrnCircle(null);
            }
        }

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

    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->circle;
    }
    public function getBeneficiaryBankName(): ?string
    {
        return $this->beneficiaryBankName;
    }

    public function setBeneficiaryBankName(?string $beneficiaryBankName): self
    {
        $this->beneficiaryBankName = $beneficiaryBankName;

        return $this;
    }

    public function getBeneficiaryAccountHolderName(): ?string
    {
        return $this->beneficiaryAccountHolderName;
    }

    public function setBeneficiaryAccountHolderName(?string $beneficiaryAccountHolderName): self
    {
        $this->beneficiaryAccountHolderName = $beneficiaryAccountHolderName;

        return $this;
    }

    public function getBeneficiaryBankAccountNumber(): ?string
    {
        return $this->beneficiaryBankAccountNumber;
    }

    public function setBeneficiaryBankAccountNumber(?string $beneficiaryBankAccountNumber): self
    {
        $this->beneficiaryBankAccountNumber = $beneficiaryBankAccountNumber;

        return $this;
    }

    public function getBeneficiaryIfscCode(): ?string
    {
        return $this->beneficiaryIfscCode;
    }

    public function setBeneficiaryIfscCode(?string $beneficiaryIfscCode): self
    {
        $this->beneficiaryIfscCode = $beneficiaryIfscCode;

        return $this;
    }

    public function getMstBankAccountTypeBeneficiary(): ?MstBankAccountType
    {
        return $this->mstBankAccountTypeBeneficiary;
    }

    public function setMstBankAccountTypeBeneficiary(?MstBankAccountType $mstBankAccountTypeBeneficiary): self
    {
        $this->mstBankAccountTypeBeneficiary = $mstBankAccountTypeBeneficiary;

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
            $trnProductMedium->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnProductMedium(TrnProductMedia $trnProductMedium): self
    {
        if ($this->trnProductMedia->contains($trnProductMedium)) {
            $this->trnProductMedia->removeElement($trnProductMedium);
            // set the owning side to null (unless already changed)
            if ($trnProductMedium->getTrnCircle() === $this) {
                $trnProductMedium->setTrnCircle(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|TrnCollectionCentreDetails[]
     */
    public function getTrnCollectionCentreDetails(): Collection
    {
        return $this->trnCollectionCentreDetails;
    }

    public function addTrnCollectionCentreDetail(TrnCollectionCentreDetails $trnCollectionCentreDetail): self
    {
        if (!$this->trnCollectionCentreDetails->contains($trnCollectionCentreDetail)) {
            $this->trnCollectionCentreDetails[] = $trnCollectionCentreDetail;
            $trnCollectionCentreDetail->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCollectionCentreDetail(TrnCollectionCentreDetails $trnCollectionCentreDetail): self
    {
        if ($this->trnCollectionCentreDetails->contains($trnCollectionCentreDetail)) {
            $this->trnCollectionCentreDetails->removeElement($trnCollectionCentreDetail);
            // set the owning side to null (unless already changed)
            if ($trnCollectionCentreDetail->getTrnCircle() === $this) {
                $trnCollectionCentreDetail->setTrnCircle(null);
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
            $trnAreaOfInterest->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnAreaOfInterest(TrnAreaOfInterest $trnAreaOfInterest): self
    {
        if ($this->trnAreaOfInterests->contains($trnAreaOfInterest)) {
            $this->trnAreaOfInterests->removeElement($trnAreaOfInterest);
            // set the owning side to null (unless already changed)
            if ($trnAreaOfInterest->getTrnCircle() === $this) {
                $trnAreaOfInterest->setTrnCircle(null);
            }
        }

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
        if(!empty($this->likeCount))
            return $this->likeCount;
        else
            return 0;
    }

    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

        return $this;
    }

    public function getShareCount(): ?int
    {
        if(!empty($this->shareCount))
            return $this->shareCount;
        else
            return 0;
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
            $trnCircleEventComment->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleEventComment(TrnCircleEventComments $trnCircleEventComment): self
    {
        if ($this->trnCircleEventComments->contains($trnCircleEventComment)) {
            $this->trnCircleEventComments->removeElement($trnCircleEventComment);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventComment->getTrnCircle() === $this) {
                $trnCircleEventComment->setTrnCircle(null);
            }
        }

        return $this;
    }

    public function getSuggestedKeywords(): ?string
    {
        return $this->suggestedKeywords;
    }

    public function setSuggestedKeywords(?string $suggestedKeywords): self
    {
        $this->suggestedKeywords = $suggestedKeywords;

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

    public function getProfileImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->profileImagePath;
    }

    public function setProfileImagePath(string $profileImagePath): self
    {
        $this->profileImagePath = $profileImagePath;

        return $this;
    }

    public function getBackgroundImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->backgroundImagePath;
    }

    public function setBackgroundImagePath(string $backgroundImagePath): self
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
            $trnCrowdFundEvent->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCrowdFundEvent(TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        if ($this->trnCrowdFundEvents->removeElement($trnCrowdFundEvent)) {
            // set the owning side to null (unless already changed)
            if ($trnCrowdFundEvent->getTrnCircle() === $this) {
                $trnCrowdFundEvent->setTrnCircle(null);
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
            $trnCircleEventBroadCastDetail->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleEventBroadCastDetail(TrnCircleEventBroadCastDetails $trnCircleEventBroadCastDetail): self
    {
        if ($this->trnCircleEventBroadCastDetails->removeElement($trnCircleEventBroadCastDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventBroadCastDetail->getTrnCircle() === $this) {
                $trnCircleEventBroadCastDetail->setTrnCircle(null);
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
            $trnCircleEventLead->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleEventLead(TrnCircleEventLeads $trnCircleEventLead): self
    {
        if ($this->trnCircleEventLeads->removeElement($trnCircleEventLead)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventLead->getTrnCircle() === $this) {
                $trnCircleEventLead->setTrnCircle(null);
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
            $trnCircleEventsVisitor->setTrnCircle($this);
        }

        return $this;
    }

    public function removeTrnCircleEventsVisitor(TrnCircleEventsVisitors $trnCircleEventsVisitor): self
    {
        if ($this->trnCircleEventsVisitors->removeElement($trnCircleEventsVisitor)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleEventsVisitor->getTrnCircle() === $this) {
                $trnCircleEventsVisitor->setTrnCircle(null);
            }
        }

        return $this;
    }
}
