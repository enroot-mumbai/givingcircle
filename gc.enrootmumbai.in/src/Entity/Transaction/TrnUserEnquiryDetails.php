<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstStatus;
use App\Entity\Master\MstUserQueryType;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnUserEnquiryDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnUserEnquiryDetailsRepository::class)
 * @ORM\Table("trnuserenquirydetails")
 */
class TrnUserEnquiryDetails
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
     * @ORM\Column(type="string", length=15)
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
    private $message;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $givingCircleResponse;

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
     * @ORM\ManyToOne(targetEntity=MstUserQueryType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstUserQueryType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLongitude;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getMstUserQueryType(): ?MstUserQueryType
    {
        return $this->mstUserQueryType;
    }

    public function setMstUserQueryType(?MstUserQueryType $mstUserQueryType): self
    {
        $this->mstUserQueryType = $mstUserQueryType;

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
}
