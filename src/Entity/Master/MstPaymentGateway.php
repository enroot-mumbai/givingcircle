<?php

namespace App\Entity\Master;

use App\Repository\Master\MstPaymentGatewayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MstPaymentGatewayRepository::class)
 * @ORM\Table("mstpaymentgateway")
 * @UniqueEntity(fields={"paymentGateway"}, message="The value is already in the system")
 */
class MstPaymentGateway
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $paymentGateway;

    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=MstPaymentGatewayDetail::class, cascade={"persist", "remove"}, mappedBy="mstPaymentGateway")
     */
    private $mstPaymentGatewayDetails;

    public function __construct()
    {
        $this->mstPaymentGatewayDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentGateway(): ?string
    {
        return $this->paymentGateway;
    }

    public function setPaymentGateway(?string $paymentGateway): self
    {
        $this->paymentGateway = $paymentGateway;

        return $this;
    }

    public function getRowId(): ?string
    {
        return $this->rowId;
    }

    public function setRowId(?string $rowId): self
    {
        $this->rowId = $rowId;

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

    /**
     * @return Collection|MstPaymentGatewayDetail[]
     */
    public function getMstPaymentGatewayDetails(): Collection
    {
        return $this->mstPaymentGatewayDetails;
    }

    public function addMstPaymentGatewayDetail(MstPaymentGatewayDetail $mstPaymentGatewayDetail): self
    {
        if (!$this->mstPaymentGatewayDetails->contains($mstPaymentGatewayDetail)) {
            $this->mstPaymentGatewayDetails[] = $mstPaymentGatewayDetail;
            $mstPaymentGatewayDetail->setMstPaymentGateway($this);
        }

        return $this;
    }

    public function removeMstPaymentGatewayDetail(MstPaymentGatewayDetail $mstPaymentGatewayDetail): self
    {
        if ($this->mstPaymentGatewayDetails->removeElement($mstPaymentGatewayDetail)) {
            // set the owning side to null (unless already changed)
            if ($mstPaymentGatewayDetail->getMstPaymentGateway() === $this) {
                $mstPaymentGatewayDetail->setMstPaymentGateway(null);
            }
        }

        return $this;
    }
}
