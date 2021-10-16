<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventSubEventsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventSubEventsRepository::class)
 * @ORM\Table("trnvoluntercircleeventsubevents")
 */
class TrnVolunterCircleEventSubEvents
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventDetails::class, inversedBy="trnVolunterCircleEventSubEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $subEventName;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfHours;

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
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $subEventReccuringBy;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunteerCircleParticipationDetails::class, mappedBy="trnVolunterCircleEventSubEvent")
     */
    private $trnVolunteerCircleParticipationDetails;

    public function __construct()
    {
        $this->trnVolunteerCircleParticipationDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubEventName(): ?string
    {
        return $this->subEventName;
    }

    public function setSubEventName(string $subEventName): self
    {
        $this->subEventName = $subEventName;

        return $this;
    }

    public function getNumberOfHours(): ?int
    {
        return $this->numberOfHours;
    }

    public function setNumberOfHours(int $numberOfHours): self
    {
        $this->numberOfHours = $numberOfHours;

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
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->subEventName;
    }

    public function getSubEventReccuringBy(): ?string
    {
        return $this->subEventReccuringBy;
    }

    public function setSubEventReccuringBy(?string $subEventReccuringBy): self
    {
        $this->subEventReccuringBy = $subEventReccuringBy;

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

    /**
     * @return Collection|TrnVolunteerCircleParticipationDetails[]
     */
    public function getTrnVolunteerCircleParticipationDetails(): Collection
    {
        return $this->trnVolunteerCircleParticipationDetails;
    }

    public function addTrnVolunteerParticipationDetail(TrnVolunteerCircleParticipationDetails $trnVolunteerParticipationDetail): self
    {
        if (!$this->trnVolunteerCircleParticipationDetails->contains($trnVolunteerParticipationDetail)) {
            $this->trnVolunteerCircleParticipationDetails[] = $trnVolunteerParticipationDetail;
            $trnVolunteerParticipationDetail->setTrnVolunterCircleEventSubEvent($this);
        }

        return $this;
    }

    public function removeTrnVolunteerParticipationDetail(TrnVolunteerCircleParticipationDetails $trnVolunteerParticipationDetail): self
    {
        if ($this->trnVolunteerCircleParticipationDetails->removeElement($trnVolunteerParticipationDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnVolunteerParticipationDetail->getTrnVolunterCircleEventSubEvent() === $this) {
                $trnVolunteerParticipationDetail->setTrnVolunterCircleEventSubEvent(null);
            }
        }

        return $this;
    }
}
