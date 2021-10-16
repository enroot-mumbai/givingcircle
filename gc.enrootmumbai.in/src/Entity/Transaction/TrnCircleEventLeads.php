<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventLeadsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventLeadsRepository::class)
 * @ORM\Table("trncircleeventleads")
 */
class TrnCircleEventLeads
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
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleEventLeads")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventLeads")
     */
    private $trnCircleEvents;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $addByAppUser;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

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

    public function getAddByAppUser(): ?AppUser
    {
        return $this->addByAppUser;
    }

    public function setAddByAppUser(?AppUser $addByAppUser): self
    {
        $this->addByAppUser = $addByAppUser;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(?\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
