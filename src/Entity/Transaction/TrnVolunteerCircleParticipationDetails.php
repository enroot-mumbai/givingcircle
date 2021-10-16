<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunteerParticipationDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunteerParticipationDetailsRepository::class)
 * @ORM\Table("trnvolunteercircleparticipationdetails")
 */
class TrnVolunteerCircleParticipationDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventDetails::class, inversedBy="trnVolunteerParticipationDetails")
     */
    private $trnVolunteerCircleEventDetail;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnVolunteerParticipationDetails")
     */
    private $trnCircleEvent;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventSubEvents::class, inversedBy="trnVolunteerParticipationDetails")
     */
    private $trnVolunterCircleEventSubEvent;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfService;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $fromTime;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numberOfHours;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     */
    private $mstStatus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnVolunteerCircleEventDetail(): ?TrnVolunterCircleEventDetails
    {
        return $this->trnVolunteerCircleEventDetail;
    }

    public function setTrnVolunteerCircleEventDetail(?TrnVolunterCircleEventDetails $trnVolunteerCircleEventDetail): self
    {
        $this->trnVolunteerCircleEventDetail = $trnVolunteerCircleEventDetail;

        return $this;
    }

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

        return $this;
    }

    public function getTrnVolunterCircleEventSubEvent(): ?TrnVolunterCircleEventSubEvents
    {
        return $this->trnVolunterCircleEventSubEvent;
    }

    public function setTrnVolunterCircleEventSubEvent(?TrnVolunterCircleEventSubEvents $trnVolunterCircleEventSubEvent): self
    {
        $this->trnVolunterCircleEventSubEvent = $trnVolunterCircleEventSubEvent;

        return $this;
    }

    public function getDateOfService(): ?\DateTimeInterface
    {
        return $this->dateOfService;
    }

    public function setDateOfService(?\DateTimeInterface $dateOfService): self
    {
        $this->dateOfService = $dateOfService;

        return $this;
    }

    public function getFromTime(): ?\DateTimeInterface
    {
        return $this->fromTime;
    }

    public function setFromTime(?\DateTimeInterface $fromTime): self
    {
        $this->fromTime = $fromTime;

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
