<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnMaterialInKindCircleEventSubEventsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnMaterialInKindCircleEventSubEventsRepository::class)
 * @ORM\Table("trnmaterialinkindcircleeventsubevents")
 */
class TrnMaterialInKindCircleEventSubEvents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnMaterialInKindCircleEventDetails::class, inversedBy="trnMaterialInKindCircleEventSubEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnMaterialInKindCircleEventDetails;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $subEventName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $itemName;

    /**
     * @ORM\Column(type="integer")
     */
    private $itemQuantity;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $unit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnMaterialInKindCircleEventDetails(): ?TrnMaterialInKindCircleEventDetails
    {
        return $this->trnMaterialInKindCircleEventDetails;
    }

    public function setTrnMaterialInKindCircleEventDetails(?TrnMaterialInKindCircleEventDetails $trnMaterialInKindCircleEventDetails): self
    {
        $this->trnMaterialInKindCircleEventDetails = $trnMaterialInKindCircleEventDetails;

        return $this;
    }

    public function getSubEventName(): ?string
    {
        return $this->subEventName;
    }

    public function setSubEventName(string $subEventName): self
    {
        $this->subEventName = $subEventName;

        return $this;
    }

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(string $itemName): self
    {
        $this->itemName = $itemName;

        return $this;
    }

    public function getItemQuantity(): ?int
    {
        return $this->itemQuantity;
    }

    public function setItemQuantity(int $itemQuantity): self
    {
        $this->itemQuantity = $itemQuantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

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
