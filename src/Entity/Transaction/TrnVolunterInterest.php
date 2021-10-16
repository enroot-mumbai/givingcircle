<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstActivityTypes;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterInterestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterInterestRepository::class)
 * @ORM\Table("trnvolunterinterest")
 */
class TrnVolunterInterest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstActivityTypes::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstActivityTypes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterDetail::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterDetail;

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
     * @ORM\ManyToMany(targetEntity=MstAreaInterest::class)
     */
    private $areaInterestSecondary;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     */
    private $areaInterestPrimary;

    public function __construct()
    {
        $this->areaInterestSecondary = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMstActivityTypes(): ?MstActivityTypes
    {
        return $this->mstActivityTypes;
    }

    public function setMstActivityTypes(?MstActivityTypes $mstActivityTypes): self
    {
        $this->mstActivityTypes = $mstActivityTypes;

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

    public function getTrnVolunterDetail(): ?TrnVolunterDetail
    {
        return $this->trnVolunterDetail;
    }

    public function setTrnVolunterDetail(?TrnVolunterDetail $trnVolunterDetail): self
    {
        $this->trnVolunterDetail = $trnVolunterDetail;

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
     * @return Collection|MstAreaInterest[]
     */
    public function getAreaInterestSecondary(): Collection
    {
        return $this->areaInterestSecondary;
    }

    public function addAreaInterestSecondary(MstAreaInterest $areaInterestSecondary): self
    {
        if (!$this->areaInterestSecondary->contains($areaInterestSecondary)) {
            $this->areaInterestSecondary[] = $areaInterestSecondary;
        }

        return $this;
    }

    public function removeAreaInterestSecondary(MstAreaInterest $areaInterestSecondary): self
    {
        $this->areaInterestSecondary->removeElement($areaInterestSecondary);

        return $this;
    }

    public function getAreaInterestPrimary(): ?MstAreaInterest
    {
        return $this->areaInterestPrimary;
    }

    public function setAreaInterestPrimary(?MstAreaInterest $areaInterestPrimary): self
    {
        $this->areaInterestPrimary = $areaInterestPrimary;

        return $this;
    }
}
