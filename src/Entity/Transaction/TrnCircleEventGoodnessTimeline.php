<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventGoodnessTimelineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventGoodnessTimelineRepository::class)
 * @ORM\Table("trncircleeventgoodnesstimeline")
 */
class TrnCircleEventGoodnessTimeline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventGoodnessTimelines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircleEvent;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfParticipant;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $targetAchieved;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $successStatement;

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
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstStatus;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumberOfParticipant(): ?int
    {
        return $this->numberOfParticipant;
    }

    public function setNumberOfParticipant(int $numberOfParticipant): self
    {
        $this->numberOfParticipant = $numberOfParticipant;

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

    public function getTargetAchieved(): ?string
    {
        return $this->targetAchieved;
    }

    public function setTargetAchieved(string $targetAchieved): self
    {
        $this->targetAchieved = $targetAchieved;

        return $this;
    }

    public function getSuccessStatement(): ?string
    {
        return $this->successStatement;
    }

    public function setSuccessStatement(string $successStatement): self
    {
        $this->successStatement = $successStatement;

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
