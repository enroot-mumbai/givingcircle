<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnMaterialInKindCircleEventCollectionCentreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnMaterialInKindCircleEventCollectionCentreRepository::class)
 * @ORM\Table("trnmaterialinkindcircleeventcollectioncentre")
 */
class TrnMaterialInKindCircleEventCollectionCentre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCollectionCentreDetails::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCollectionCentreDetails;

    /**
     * @ORM\ManyToOne(targetEntity=TrnMaterialInKindCircleEventDetails::class, inversedBy="trnMaterialInKindCircleEventCollectionCentres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnMaterialInKindCircleEventDetails;

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

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $toDate;

    public function getId(): ?int
    {
        return $this->id;
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
