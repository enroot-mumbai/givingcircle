<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventRequestToParticipateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventRequestToParticipateRepository::class)
 * @ORM\Table("trncircleeventrequesttoparticipate")
 */
class TrnCircleEventRequestToParticipate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventRequestToParticipates")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnCircleEvent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requestedOn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $mobileCountryCode;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $mobileNumber;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstStatus;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventInvitations::class, mappedBy="trnCircleEventRequestToParticipate")
     */
    private $trnCircleEventInvitations;

    /**
     * @ORM\ManyToOne(targetEntity=MstEventProductType::class)
     */
    private $mstProductType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $statusUpdatedOn;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUserStatusUpdatedBy;

    public function __construct()
    {
        $this->trnCircleEventInvitations = new ArrayCollection();
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

    public function getRequestedOn(): ?\DateTimeInterface
    {
        return $this->requestedOn;
    }

    public function setRequestedOn(\DateTimeInterface $requestedOn): self
    {
        $this->requestedOn = $requestedOn;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getMobileCountryCode(): ?string
    {
        return $this->mobileCountryCode;
    }

    public function setMobileCountryCode(string $mobileCountryCode): self
    {
        $this->mobileCountryCode = $mobileCountryCode;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

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

    public function getOrgCompany(): ?OrgCompany
    {
        return $this->orgCompany;
    }

    public function setOrgCompany(?OrgCompany $orgCompany): self
    {
        $this->orgCompany = $orgCompany;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
            $trnCircleEventInvitation->setTrnCircleEventRequestToParticipate($this);
        }

        return $this;
    }

    public function removeTrnCircleEventInvitation(TrnCircleEventInvitations $trnCircleEventInvitation): self
    {
        if ($this->trnCircleEventInvitations->contains($trnCircleEventInvitation)) {
            $this->trnCircleEventInvitations->removeElement($trnCircleEventInvitation);
            // set the owning side to null (unless already changed)
            if ($trnCircleEventInvitation->getTrnCircleEventRequestToParticipate() === $this) {
                $trnCircleEventInvitation->setTrnCircleEventRequestToParticipate(null);
            }
        }

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

    public function getMstProductType(): ?MstEventProductType
    {
        return $this->mstProductType;
    }

    public function setMstProductType(?MstEventProductType $mstProductType): self
    {
        $this->mstProductType = $mstProductType;

        return $this;
    }

    public function getStatusUpdatedOn(): ?\DateTimeInterface
    {
        return $this->statusUpdatedOn;
    }

    public function setStatusUpdatedOn(?\DateTimeInterface $statusUpdatedOn): self
    {
        $this->statusUpdatedOn = $statusUpdatedOn;

        return $this;
    }

    public function getAppUserStatusUpdatedBy(): ?AppUser
    {
        return $this->appUserStatusUpdatedBy;
    }

    public function setAppUserStatusUpdatedBy(?AppUser $appUserStatusUpdatedBy): self
    {
        $this->appUserStatusUpdatedBy = $appUserStatusUpdatedBy;

        return $this;
    }
}
