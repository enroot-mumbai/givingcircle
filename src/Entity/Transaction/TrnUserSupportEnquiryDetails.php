<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnUserSupportEnquiryDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnUserSupportEnquiryDetailsRepository::class)
 * @ORM\Table("trnusersupportenquirydetails")
 */
class TrnUserSupportEnquiryDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $mobileCountryCode;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $mobileNumber;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $cause;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $doingOnItToday;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $whatYouAreAimingAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $givingCircleResponse;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstStatus;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUserRepliedBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $repliedOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getDoingOnItToday(): ?string
    {
        return $this->doingOnItToday;
    }

    public function setDoingOnItToday(string $doingOnItToday): self
    {
        $this->doingOnItToday = $doingOnItToday;

        return $this;
    }

    public function getWhatYouAreAimingAt(): ?string
    {
        return $this->whatYouAreAimingAt;
    }

    public function setWhatYouAreAimingAt(string $whatYouAreAimingAt): self
    {
        $this->whatYouAreAimingAt = $whatYouAreAimingAt;

        return $this;
    }

    public function getGivingCircleResponse(): ?string
    {
        return $this->givingCircleResponse;
    }

    public function setGivingCircleResponse(?string $givingCircleResponse): self
    {
        $this->givingCircleResponse = $givingCircleResponse;

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

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

        return $this;
    }

    public function getAppUserRepliedBy(): ?AppUser
    {
        return $this->appUserRepliedBy;
    }

    public function setAppUserRepliedBy(?AppUser $appUserRepliedBy): self
    {
        $this->appUserRepliedBy = $appUserRepliedBy;

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

    public function getRepliedOn(): ?\DateTimeInterface
    {
        return $this->repliedOn;
    }

    public function setRepliedOn(?\DateTimeInterface $repliedOn): self
    {
        $this->repliedOn = $repliedOn;

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
}
