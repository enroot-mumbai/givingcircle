<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Repository\Transaction\TrnAreaOfInterestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnAreaOfInterestRepository::class)
 * @ORM\Table("trnareaofinterest")
 */
class TrnAreaOfInterest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     */
    private $areaInterestPrimary;

    /**
     * @ORM\ManyToMany(targetEntity=MstAreaInterest::class)
     */
    private $areaInterestSecondary;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnAreaOfInterests")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnAreaOfInterests")
     */
    private $trnCircleEvents;

    public function __construct()
    {
        $this->areaInterestSecondary = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        if ($this->areaInterestSecondary->contains($areaInterestSecondary)) {
            $this->areaInterestSecondary->removeElement($areaInterestSecondary);
        }

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

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->areaInterestPrimary->getAreaInterest();
    }
}
