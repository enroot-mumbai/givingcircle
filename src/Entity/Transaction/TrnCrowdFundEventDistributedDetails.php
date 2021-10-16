<?php

namespace App\Entity\Transaction;

use App\Repository\Transaction\TrnCrowdFundEventDistributedDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCrowdFundEventDistributedDetailsRepository::class)
 * @ORM\Table("trncrowdfundeventdistributeddetails")
 */
class TrnCrowdFundEventDistributedDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCrowdFundEvent::class, inversedBy="trnCrowdFundEventDistributedDetails")
     */
    private $trnCrowdFundEvent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $campaignerName;

    /**
     * @ORM\Column(type="string", length=12, nullable=true)
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $campaingerEmail;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $targetAmount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnCrowdFundEvent(): ?TrnCrowdFundEvent
    {
        return $this->trnCrowdFundEvent;
    }

    public function setTrnCrowdFundEvent(?TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        $this->trnCrowdFundEvent = $trnCrowdFundEvent;

        return $this;
    }

    public function getCampaignerName(): ?string
    {
        return $this->campaignerName;
    }

    public function setCampaignerName(?string $campaignerName): self
    {
        $this->campaignerName = $campaignerName;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    public function getCampaingerEmail(): ?string
    {
        return $this->campaingerEmail;
    }

    public function setCampaingerEmail(?string $campaingerEmail): self
    {
        $this->campaingerEmail = $campaingerEmail;

        return $this;
    }

    public function getTargetAmount(): ?string
    {
        return $this->targetAmount;
    }

    public function setTargetAmount(?string $targetAmount): self
    {
        $this->targetAmount = $targetAmount;

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
