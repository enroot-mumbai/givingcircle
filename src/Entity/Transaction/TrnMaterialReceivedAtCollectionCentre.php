<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnMaterialReceivedAtCollectionCentreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnMaterialReceivedAtCollectionCentreRepository::class)
 * @ORM\Table("trnmaterialreceivedatcollectioncentre")
 */
class TrnMaterialReceivedAtCollectionCentre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnMaterialReceivedAtCollectionCentres")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnCircleEvents;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCollectionCentreDetails::class, inversedBy="trnMaterialReceivedAtCollectionCentres")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnCollectionCentreDetails;

    /**
     * @ORM\ManyToOne(targetEntity=TrnMaterialInKindCircleEventDetails::class, inversedBy="trnMaterialReceivedAtCollectionCentres")
     */
    private $trnMaterialInKindCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $itemName;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $itemQuantity;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $itemUnit;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $remarks;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isReceived;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receivedOn;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     */
    private $mstStatus;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTrnCollectionCentreDetails(): ?TrnCollectionCentreDetails
    {
        return $this->trnCollectionCentreDetails;
    }

    public function setTrnCollectionCentreDetails(?TrnCollectionCentreDetails $trnCollectionCentreDetails): self
    {
        $this->trnCollectionCentreDetails = $trnCollectionCentreDetails;

        return $this;
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

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(string $itemName): self
    {
        $this->itemName = $itemName;

        return $this;
    }

    public function getItemQuantity(): ?string
    {
        return $this->itemQuantity;
    }

    public function setItemQuantity(string $itemQuantity): self
    {
        $this->itemQuantity = $itemQuantity;

        return $this;
    }

    public function getItemUnit(): ?string
    {
        return $this->itemUnit;
    }

    public function setItemUnit(string $itemUnit): self
    {
        $this->itemUnit = $itemUnit;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

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
    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = null;
    }

    public function getIsReceived(): ?bool
    {
        return $this->isReceived;
    }

    public function setIsReceived(?bool $isReceived): self
    {
        $this->isReceived = $isReceived;

        return $this;
    }

    public function getReceivedOn(): ?\DateTimeInterface
    {
        return $this->receivedOn;
    }

    public function setReceivedOn(?\DateTimeInterface $receivedOn): self
    {
        $this->receivedOn = $receivedOn;

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
}
