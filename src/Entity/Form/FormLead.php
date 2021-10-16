<?php

namespace App\Entity\Form;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstLeadStatus;
use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use App\Repository\Form\FormLeadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormLeadRepository::class)
 * @ORM\Table("formlead")
 */
class FormLead
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MstSalutation::class)
     */
    private $mstSalutation;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $leadFirstName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $leadMiddleName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $leadLastName;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $leadEmailAddress;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $leadMobileNumber;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $leadPhoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leadAddressOne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leadAddressTwo;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $leadPincode;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $leadCity;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     */
    private $mstCountry;

    /**
     * @ORM\Column(type="datetime")
     */
    private $leadCreateTime;

    /**
     * @ORM\ManyToOne(targetEntity=MstLeadStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstLeadStatus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $leadDescription;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMstSalutation(): ?MstSalutation
    {
        return $this->mstSalutation;
    }

    public function setMstSalutation(?MstSalutation $mstSalutation): self
    {
        $this->mstSalutation = $mstSalutation;

        return $this;
    }

    public function getLeadFirstName(): ?string
    {
        return $this->leadFirstName;
    }

    public function setLeadFirstName(?string $leadFirstName): self
    {
        $this->leadFirstName = $leadFirstName;

        return $this;
    }

    public function getLeadMiddleName(): ?string
    {
        return $this->leadMiddleName;
    }

    public function setLeadMiddleName(?string $leadMiddleName): self
    {
        $this->leadMiddleName = $leadMiddleName;

        return $this;
    }

    public function getLeadLastName(): ?string
    {
        return $this->leadLastName;
    }

    public function setLeadLastName(?string $leadLastName): self
    {
        $this->leadLastName = $leadLastName;

        return $this;
    }

    public function getLeadEmailAddress(): ?string
    {
        return $this->leadEmailAddress;
    }

    public function setLeadEmailAddress(?string $leadEmailAddress): self
    {
        $this->leadEmailAddress = $leadEmailAddress;

        return $this;
    }

    public function getLeadMobileNumber(): ?string
    {
        return $this->leadMobileNumber;
    }

    public function setLeadMobileNumber(?string $leadMobileNumber): self
    {
        $this->leadMobileNumber = $leadMobileNumber;

        return $this;
    }

    public function getLeadPhoneNumber(): ?string
    {
        return $this->leadPhoneNumber;
    }

    public function setLeadPhoneNumber(?string $leadPhoneNumber): self
    {
        $this->leadPhoneNumber = $leadPhoneNumber;

        return $this;
    }

    public function getLeadAddressOne(): ?string
    {
        return $this->leadAddressOne;
    }

    public function setLeadAddressOne(?string $leadAddressOne): self
    {
        $this->leadAddressOne = $leadAddressOne;

        return $this;
    }

    public function getLeadAddressTwo(): ?string
    {
        return $this->leadAddressTwo;
    }

    public function setLeadAddressTwo(?string $leadAddressTwo): self
    {
        $this->leadAddressTwo = $leadAddressTwo;

        return $this;
    }

    public function getLeadPincode(): ?string
    {
        return $this->leadPincode;
    }

    public function setLeadPincode(?string $leadPincode): self
    {
        $this->leadPincode = $leadPincode;

        return $this;
    }

    public function getLeadCity(): ?string
    {
        return $this->leadCity;
    }

    public function setLeadCity(?string $leadCity): self
    {
        $this->leadCity = $leadCity;

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

    public function getLeadCreateTime(): ?\DateTimeInterface
    {
        return $this->leadCreateTime;
    }

    public function setLeadCreateTime(\DateTimeInterface $leadCreateTime): self
    {
        $this->leadCreateTime = $leadCreateTime;

        return $this;
    }

    public function getMstLeadStatus(): ?MstLeadStatus
    {
        return $this->mstLeadStatus;
    }

    public function setMstLeadStatus(?MstLeadStatus $mstLeadStatus): self
    {
        $this->mstLeadStatus = $mstLeadStatus;

        return $this;
    }

    public function getLeadDescription(): ?string
    {
        return $this->leadDescription;
    }

    public function setLeadDescription(?string $leadDescription): self
    {
        $this->leadDescription = $leadDescription;

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
