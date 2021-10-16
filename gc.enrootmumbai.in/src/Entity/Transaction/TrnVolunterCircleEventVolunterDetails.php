<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventVolunterDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventVolunterDetailsRepository::class)
 * @ORM\Table("trnvoluntercircleeventvolunterdetails")
 */
class TrnVolunterCircleEventVolunterDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnVolunterCircleEventVolunterDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircleEvents;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventDetails::class, inversedBy="trnVolunterCircleEventVolunterDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterDetail::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterDetail;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstStatus;

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
     * @ORM\OneToMany(targetEntity=TrnVolunterCircleEventVolunterAvailabilityDetails::class, mappedBy="trnVolunterCircleEventVolunterDetails", orphanRemoval=true)
     */
    private $trnVolunterCircleEventVolunterAvailabilityDetails;

    public function __construct()
    {
        $this->trnVolunterCircleEventVolunterAvailabilityDetails = new ArrayCollection();
    }

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

    public function getTrnVolunterCircleEventDetails(): ?TrnVolunterCircleEventDetails
    {
        return $this->trnVolunterCircleEventDetails;
    }

    public function setTrnVolunterCircleEventDetails(?TrnVolunterCircleEventDetails $trnVolunterCircleEventDetails): self
    {
        $this->trnVolunterCircleEventDetails = $trnVolunterCircleEventDetails;

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

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

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

    /**
     * @return Collection|TrnVolunterCircleEventVolunterAvailabilityDetails[]
     */
    public function getTrnVolunterCircleEventVolunterAvailabilityDetails(): Collection
    {
        return $this->trnVolunterCircleEventVolunterAvailabilityDetails;
    }

    public function addTrnVolunterCircleEventVolunterAvailabilityDetail(TrnVolunterCircleEventVolunterAvailabilityDetails $trnVolunterCircleEventVolunterAvailabilityDetail): self
    {
        if (!$this->trnVolunterCircleEventVolunterAvailabilityDetails->contains($trnVolunterCircleEventVolunterAvailabilityDetail)) {
            $this->trnVolunterCircleEventVolunterAvailabilityDetails[] = $trnVolunterCircleEventVolunterAvailabilityDetail;
            $trnVolunterCircleEventVolunterAvailabilityDetail->setTrnVolunterCircleEventVolunterDetails($this);
        }

        return $this;
    }

    public function removeTrnVolunterCircleEventVolunterAvailabilityDetail(TrnVolunterCircleEventVolunterAvailabilityDetails $trnVolunterCircleEventVolunterAvailabilityDetail): self
    {
        if ($this->trnVolunterCircleEventVolunterAvailabilityDetails->contains($trnVolunterCircleEventVolunterAvailabilityDetail)) {
            $this->trnVolunterCircleEventVolunterAvailabilityDetails->removeElement($trnVolunterCircleEventVolunterAvailabilityDetail);
            // set the owning side to null (unless already changed)
            if ($trnVolunterCircleEventVolunterAvailabilityDetail->getTrnVolunterCircleEventVolunterDetails() === $this) {
                $trnVolunterCircleEventVolunterAvailabilityDetail->setTrnVolunterCircleEventVolunterDetails(null);
            }
        }

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
