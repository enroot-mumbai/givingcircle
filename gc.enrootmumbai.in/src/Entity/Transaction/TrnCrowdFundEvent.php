<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCurrency;
use App\Repository\Transaction\TrnCrowdFundEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCrowdFundEventRepository::class)
 * @ORM\Table("trncrowdfundevent")
 */
class TrnCrowdFundEvent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCrowdFundEvents")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCrowdFundEvents")
     */
    private $trnCircleEvent;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $targetAmount;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $mstTargetAmountCurrency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $minimumContribution;

    /**
     * @ORM\ManyToOne(targetEntity=MstCurrency::class)
     */
    private $mstMinimumContributionCurrency;

    /**
     * @ORM\OneToMany(targetEntity=TrnCrowdFundEventDocuments::class, mappedBy="trnCrowdFundEvent")
     */
    private $trnCrowdFundEventDocuments;

    /**
     * @ORM\OneToMany(targetEntity=TrnCrowdFundEventDistributedDetails::class, mappedBy="trnCrowdFundEvent")
     */
    private $trnCrowdFundEventDistributedDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnCrowdFundEventOfflineTransfer::class, mappedBy="trncrowdfundeventofflinetransfer")
     */
    private $trnCrowdFundEventOfflineTransfer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDistributedEvent;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrder::class, mappedBy="trnCrowdFundEvent")
     */
    private $trnOrders;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrderDetail::class, mappedBy="trnCrowdFundEvent")
     */
    private $trnOrderDetails;

    public function __construct()
    {
        $this->trnCrowdFundEventDocuments = new ArrayCollection();
        $this->trnCrowdFundEventDistributedDetails = new ArrayCollection();
        $this->trnCrowdFundEventOfflineTransfer = new ArrayCollection();
        $this->trnOrders = new ArrayCollection();
        $this->trnOrderDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

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

    public function getMstTargetAmountCurrency(): ?MstCurrency
    {
        return $this->mstTargetAmountCurrency;
    }

    public function setMstTargetAmountCurrency(?MstCurrency $mstTargetAmountCurrency): self
    {
        $this->mstTargetAmountCurrency = $mstTargetAmountCurrency;

        return $this;
    }

    public function getMinimumContribution(): ?string
    {
        return $this->minimumContribution;
    }

    public function setMinimumContribution(?string $minimumContribution): self
    {
        $this->minimumContribution = $minimumContribution;

        return $this;
    }

    public function getMstMinimumContributionCurrency(): ?MstCurrency
    {
        return $this->mstMinimumContributionCurrency;
    }

    public function setMstMinimumContributionCurrency(?MstCurrency $mstMinimumContributionCurrency): self
    {
        $this->mstMinimumContributionCurrency = $mstMinimumContributionCurrency;

        return $this;
    }

    /**
     * @return Collection|TrnCrowdFundEventDocuments[]
     */
    public function getTrnCrowdFundEventDocuments(): Collection
    {
        return $this->trnCrowdFundEventDocuments;
    }

    public function addTrnCrowdFundEventDocument(TrnCrowdFundEventDocuments $trnCrowdFundEventDocument): self
    {
        if (!$this->trnCrowdFundEventDocuments->contains($trnCrowdFundEventDocument)) {
            $this->trnCrowdFundEventDocuments[] = $trnCrowdFundEventDocument;
            $trnCrowdFundEventDocument->setTrnCrowdFundEvent($this);
        }

        return $this;
    }

    public function removeTrnCrowdFundEventDocument(TrnCrowdFundEventDocuments $trnCrowdFundEventDocument): self
    {
        if ($this->trnCrowdFundEventDocuments->removeElement($trnCrowdFundEventDocument)) {
            // set the owning side to null (unless already changed)
            if ($trnCrowdFundEventDocument->getTrnCrowdFundEvent() === $this) {
                $trnCrowdFundEventDocument->setTrnCrowdFundEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCrowdFundEventDistributedDetails[]
     */
    public function getTrnCrowdFundEventDistributedDetails(): Collection
    {
        return $this->trnCrowdFundEventDistributedDetails;
    }

    public function addTrnCrowdFundEventDistributedDetail(TrnCrowdFundEventDistributedDetails $trnCrowdFundEventDistributedDetail): self
    {
        if (!$this->trnCrowdFundEventDistributedDetails->contains($trnCrowdFundEventDistributedDetail)) {
            $this->trnCrowdFundEventDistributedDetails[] = $trnCrowdFundEventDistributedDetail;
            $trnCrowdFundEventDistributedDetail->setTrnCrowdFundEvent($this);
        }

        return $this;
    }

    public function removeTrnCrowdFundEventDistributedDetail(TrnCrowdFundEventDistributedDetails $trnCrowdFundEventDistributedDetail): self
    {
        if ($this->trnCrowdFundEventDistributedDetails->removeElement($trnCrowdFundEventDistributedDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnCrowdFundEventDistributedDetail->getTrnCrowdFundEvent() === $this) {
                $trnCrowdFundEventDistributedDetail->setTrnCrowdFundEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnCrowdFundEventOfflineTransfer[]
     */
    public function getTrnCrowdFundEventOfflineTransfer(): Collection
    {
        return $this->trnCrowdFundEventOfflineTransfer;
    }

    public function getIsDistributedEvent(): ?bool
    {
        return $this->isDistributedEvent;
    }

    public function setIsDistributedEvent(?bool $isDistributedEvent): self
    {
        $this->isDistributedEvent = $isDistributedEvent;

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
     * @return Collection|TrnOrder[]
     */
    public function getTrnOrders(): Collection
    {
        return $this->trnOrders;
    }

    public function addTrnOrder(TrnOrder $trnOrder): self
    {
        if (!$this->trnOrders->contains($trnOrder)) {
            $this->trnOrders[] = $trnOrder;
            $trnOrder->setTrnCrowdFundEvent($this);
        }

        return $this;
    }

    public function removeTrnOrder(TrnOrder $trnOrder): self
    {
        if ($this->trnOrders->removeElement($trnOrder)) {
            // set the owning side to null (unless already changed)
            if ($trnOrder->getTrnCrowdFundEvent() === $this) {
                $trnOrder->setTrnCrowdFundEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnOrderDetail[]
     */
    public function getTrnOrderDetails(): Collection
    {
        return $this->trnOrderDetails;
    }

    public function addTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if (!$this->trnOrderDetails->contains($trnOrderDetail)) {
            $this->trnOrderDetails[] = $trnOrderDetail;
            $trnOrderDetail->setTrnCrowdFundEvent($this);
        }

        return $this;
    }

    public function removeTrnOrderDetail(TrnOrderDetail $trnOrderDetail): self
    {
        if ($this->trnOrderDetails->removeElement($trnOrderDetail)) {
            // set the owning side to null (unless already changed)
            if ($trnOrderDetail->getTrnCrowdFundEvent() === $this) {
                $trnOrderDetail->setTrnCrowdFundEvent(null);
            }
        }

        return $this;
    }
}
