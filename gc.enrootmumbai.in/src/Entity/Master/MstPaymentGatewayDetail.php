<?php

namespace App\Entity\Master;

use App\Repository\Master\MstPaymentGatewayDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MstPaymentGatewayDetailRepository::class)
 * @ORM\Table("mstpaymentgatewaydetail")
 */
class MstPaymentGatewayDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $paymentKey;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $paymentKeyValue;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $paymentGatewayEnv;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstPaymentGateway::class, inversedBy="mstPaymentGatewayDetails")
     */
    private $mstPaymentGateway;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentKey(): ?string
    {
        return $this->paymentKey;
    }

    public function setPaymentKey(?string $paymentKey): self
    {
        $this->paymentKey = $paymentKey;

        return $this;
    }

    public function getPaymentKeyValue(): ?string
    {
        return $this->paymentKeyValue;
    }

    public function setPaymentKeyValue(?string $paymentKeyValue): self
    {
        $this->paymentKeyValue = $paymentKeyValue;

        return $this;
    }

    public function getPaymentGatewayEnv(): ?string
    {
        return $this->paymentGatewayEnv;
    }

    public function setPaymentGatewayEnv(?string $paymentGatewayEnv): self
    {
        $this->paymentGatewayEnv = $paymentGatewayEnv;

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

    public function getMstPaymentGateway(): ?MstPaymentGateway
    {
        return $this->mstPaymentGateway;
    }

    public function setMstPaymentGateway(?MstPaymentGateway $mstPaymentGateway): self
    {
        $this->mstPaymentGateway = $mstPaymentGateway;

        return $this;
    }
}
