<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventRecurringDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventRecurringDetailsRepository::class)
 * @ORM\Table("trncircleeventrecurringdetails")
 */
class TrnCircleEventRecurringDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfDays;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventRecurringDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircleEvents;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventDetails::class, inversedBy="trnCircleEventRecurringDetails")
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\ManyToMany(targetEntity=MstDaysOfWeek::class)
     */
    private $mstDaysOfWeek;

    /**
     * @ORM\ManyToOne(targetEntity=TrnMaterialInKindCircleEventDetails::class, inversedBy="trnCircleEventRecurringDetails")
     */
    private $trnMaterialInKindCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=TrnFundRaiserCircleEventDetails::class, inversedBy="trnCircleEventRecurringDetails")
     */
    private $trnFundRaiserCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=MstRecurringBy::class)
     */
    private $mstRecurringBy;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numberOfHours;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $datesSelected;

    public function __construct()
    {
        $this->mstDaysOfWeek = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    
    public function getNumberOfDays(): ?int
    {
        return $this->numberOfDays;
    }

    public function setNumberOfDays(?int $numberOfDays): self
    {
        $this->numberOfDays = $numberOfDays;

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

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
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

    public function getTrnVolunterCircleEventDetails(): ?TrnVolunterCircleEventDetails
    {
        return $this->trnVolunterCircleEventDetails;
    }

    public function setTrnVolunterCircleEventDetails(?TrnVolunterCircleEventDetails $trnVolunterCircleEventDetails): self
    {
        $this->trnVolunterCircleEventDetails = $trnVolunterCircleEventDetails;

        return $this;
    }

    /**
     * @return Collection|MstDaysOfWeek[]
     */
    public function getMstDaysOfWeek(): Collection
    {
        return $this->mstDaysOfWeek;
    }

    public function addMstDaysOfWeek(MstDaysOfWeek $mstDaysOfWeek): self
    {
        if (!$this->mstDaysOfWeek->contains($mstDaysOfWeek)) {
            $this->mstDaysOfWeek[] = $mstDaysOfWeek;
        }

        return $this;
    }

    public function removeMstDaysOfWeek(MstDaysOfWeek $mstDaysOfWeek): self
    {
        if ($this->mstDaysOfWeek->contains($mstDaysOfWeek)) {
            $this->mstDaysOfWeek->removeElement($mstDaysOfWeek);
        }

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

    public function getTrnFundRaiserCircleEventDetails(): ?TrnFundRaiserCircleEventDetails
    {
        return $this->trnFundRaiserCircleEventDetails;
    }

    public function setTrnFundRaiserCircleEventDetails(?TrnFundRaiserCircleEventDetails $trnFundRaiserCircleEventDetails): self
    {
        $this->trnFundRaiserCircleEventDetails = $trnFundRaiserCircleEventDetails;

        return $this;
    }

    public function getMstRecurringBy(): ?MstRecurringBy
    {
        return $this->mstRecurringBy;
    }

    public function setMstRecurringBy(?MstRecurringBy $mstRecurringBy): self
    {
        $this->mstRecurringBy = $mstRecurringBy;

        return $this;
    }

    public function getNumberOfHours(): ?int
    {
        return $this->numberOfHours;
    }

    public function setNumberOfHours(?int $numberOfHours): self
    {
        $this->numberOfHours = $numberOfHours;

        return $this;
    }

    public function getDatesSelected(): ?string
    {
        return $this->datesSelected;
    }

    public function setDatesSelected(?string $datesSelected): self
    {
        $this->datesSelected = $datesSelected;

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
