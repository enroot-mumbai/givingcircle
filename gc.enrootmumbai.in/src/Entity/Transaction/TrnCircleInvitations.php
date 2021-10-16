<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleInvitationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleInvitationsRepository::class)
 * @ORM\Table("trncircleinvitations")
 */
class TrnCircleInvitations
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleInvitations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnCircle;

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
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleRequestToJoin::class)
     */
    private $trnCircleRequestToJoin;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUserInvitedBy;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUserInvite;

    /**
     * @ORM\ManyToOne(targetEntity=TrnAppUserContacts::class, inversedBy="trnCircleInvitations")
     */
    private $trnAppUserContacts;

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

    public function setMobileNumber(string $mobileNumber): self
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

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

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

    public function getOrgCompany(): ?OrgCompany
    {
        return $this->orgCompany;
    }

    public function setOrgCompany(?OrgCompany $orgCompany): self
    {
        $this->orgCompany = $orgCompany;

        return $this;
    }

    public function getTrnCircleRequestToJoin(): ?TrnCircleRequestToJoin
    {
        return $this->trnCircleRequestToJoin;
    }

    public function setTrnCircleRequestToJoin(?TrnCircleRequestToJoin $trnCircleRequestToJoin): self
    {
        $this->trnCircleRequestToJoin = $trnCircleRequestToJoin;

        return $this;
    }

    public function getAppUserInvitedBy(): ?AppUser
    {
        return $this->appUserInvitedBy;
    }

    public function setAppUserInvitedBy(?AppUser $appUserInvitedBy): self
    {
        $this->appUserInvitedBy = $appUserInvitedBy;

        return $this;
    }

    public function getAppUserInvite(): ?AppUser
    {
        return $this->appUserInvite;
    }

    public function setAppUserInvite(?AppUser $appUserInvite): self
    {
        $this->appUserInvite = $appUserInvite;

        return $this;
    }

    public function getTrnAppUserContacts(): ?TrnAppUserContacts
    {
        return $this->trnAppUserContacts;
    }

    public function setTrnAppUserContacts(?TrnAppUserContacts $trnAppUserContacts): self
    {
        $this->trnAppUserContacts = $trnAppUserContacts;

        return $this;
    }
}
