<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventDeactivatingReasonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventDeactivatingReasonRepository::class)
 * @ORM\Table("trncircleeventdeactivatingreason")
 */
class TrnCircleEventDeactivatingReason
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $deactivatedByAppUser;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deactivatedOn;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $deactivatingReason;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventDeactivatingReasons")
     */
    private $trnCircleEvents;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeactivatedByAppUser(): ?AppUser
    {
        return $this->deactivatedByAppUser;
    }

    public function setDeactivatedByAppUser(?AppUser $deactivatedByAppUser): self
    {
        $this->deactivatedByAppUser = $deactivatedByAppUser;

        return $this;
    }

    public function getDeactivatedOn(): ?\DateTimeInterface
    {
        return $this->deactivatedOn;
    }

    public function setDeactivatedOn(?\DateTimeInterface $deactivatedOn): self
    {
        $this->deactivatedOn = $deactivatedOn;

        return $this;
    }

    public function getDeactivatingReason(): ?string
    {
        return $this->deactivatingReason;
    }

    public function setDeactivatingReason(?string $deactivatingReason): self
    {
        $this->deactivatingReason = $deactivatingReason;

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
}
