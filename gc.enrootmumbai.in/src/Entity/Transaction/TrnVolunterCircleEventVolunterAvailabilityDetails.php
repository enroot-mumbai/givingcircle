<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventVolunterAvailabilityDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventVolunterAvailabilityDetailsRepository::class)
 * @ORM\Table("trnvoluntercircleeventvolunteravailabilitydetails")
 */
class TrnVolunterCircleEventVolunterAvailabilityDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventVolunterDetails::class, inversedBy="trnVolunterCircleEventVolunterAvailabilityDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterCircleEventVolunterDetails;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $availableOnDay;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $availableOnTime;

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

    public function getTrnVolunterCircleEventVolunterDetails(): ?TrnVolunterCircleEventVolunterDetails
    {
        return $this->trnVolunterCircleEventVolunterDetails;
    }

    public function setTrnVolunterCircleEventVolunterDetails(?TrnVolunterCircleEventVolunterDetails $trnVolunterCircleEventVolunterDetails): self
    {
        $this->trnVolunterCircleEventVolunterDetails = $trnVolunterCircleEventVolunterDetails;

        return $this;
    }

    public function getAvailableOnDay(): ?string
    {
        return $this->availableOnDay;
    }

    public function setAvailableOnDay(string $availableOnDay): self
    {
        $this->availableOnDay = $availableOnDay;

        return $this;
    }

    public function getAvailableOnTime(): ?string
    {
        return $this->availableOnTime;
    }

    public function setAvailableOnTime(string $availableOnTime): self
    {
        $this->availableOnTime = $availableOnTime;

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
