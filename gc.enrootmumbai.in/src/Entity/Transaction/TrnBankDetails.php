<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstBankAccountType;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnBankDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnBankDetailsRepository::class)
 * @ORM\Table("trnbankdetails")
 */
class TrnBankDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountHolderName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ifscCode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstBankAccountType::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstBankAccountType;

    /**
     * @ORM\ManyToOne(targetEntity=TrnOrganizationDetails::class, inversedBy="trnBankDetails")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnOrganizationDetails;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bankName;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class, inversedBy="trnBankDetails")
     */
    private $appUserMemberDetails;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountHolderName(): ?string
    {
        return $this->accountHolderName;
    }

    public function setAccountHolderName(?string $accountHolderName): self
    {
        $this->accountHolderName = $accountHolderName;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getIfscCode(): ?string
    {
        return $this->ifscCode;
    }

    public function setIfscCode(?string $ifscCode): self
    {
        $this->ifscCode = $ifscCode;

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

    public function getMstBankAccountType(): ?MstBankAccountType
    {
        return $this->mstBankAccountType;
    }

    public function setMstBankAccountType(?MstBankAccountType $mstBankAccountType): self
    {
        $this->mstBankAccountType = $mstBankAccountType;

        return $this;
    }

    public function getTrnOrganizationDetails(): ?TrnOrganizationDetails
    {
        return $this->trnOrganizationDetails;
    }

    public function setTrnOrganizationDetails(?TrnOrganizationDetails $trnOrganizationDetails): self
    {
        $this->trnOrganizationDetails = $trnOrganizationDetails;

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getAppUserMemberDetails(): ?AppUser
    {
        return $this->appUserMemberDetails;
    }

    public function setAppUserMemberDetails(?AppUser $appUserMemberDetails): self
    {
        $this->appUserMemberDetails = $appUserMemberDetails;

        return $this;
    }
}
