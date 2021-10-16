<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventsVisitorsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventsVisitorsRepository::class)
 * @ORM\Table("trncircleeventsvisitors")
 */
class TrnCircleEventsVisitors
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleEventsVisitors")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventsVisitors")
     */
    private $trnCircleEvents;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $visitedOn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $userIpAddress;

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

    public function getTrnCircle(): ?TrnCircle
    {
        return $this->trnCircle;
    }

    public function setTrnCircle(?TrnCircle $trnCircle): self
    {
        $this->trnCircle = $trnCircle;

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

    public function getVisitedOn(): ?\DateTimeInterface
    {
        return $this->visitedOn;
    }

    public function setVisitedOn(?\DateTimeInterface $visitedOn): self
    {
        $this->visitedOn = $visitedOn;

        return $this;
    }

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(?string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }
}
