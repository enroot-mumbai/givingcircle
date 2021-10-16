<?php

namespace App\Entity\Master;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Master\MstStateRepository")
 * @ORM\Table("mststate")
 */
class MstState
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $fipsCode;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $iso2;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCountry;

    public function __construct()
    {
        $this->mstcity = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFipsCode():string
    {
        return $this->fipsCode;
    }

    /**
     * @param mixed $fipsCode
     */
    public function setFipsCode(?string $fipsCode): self
    {
        $this->fipsCode = $fipsCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIso2():string
    {
        return $this->iso2;
    }

    /**
     * @param mixed $iso2
     */
    public function setIso2(?string $iso2): string
    {
        $this->iso2 = $iso2;
        return $this;
    }


    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;
        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->state;
    }
}
