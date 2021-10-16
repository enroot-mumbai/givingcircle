<?php

namespace App\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Master\MstCountryRepository")
 * @UniqueEntity(fields={"country"}, message="The value is already in the system")
 * @ORM\Table("mstcountry")
 */
class MstCountry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $iso3;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $iso2;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $phoneCode;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $capital;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currency;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhoneCode(): ?string
    {
        return $this->phoneCode;
    }

    public function setPhoneCode(?string $phoneCode): self
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    public function jsonSerialize(): string
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getIso3() :string
    {
        return $this->iso3;
    }

    /**
     * @param mixed $iso3
     */
    public function setIso3(?string $iso3): self
    {
        $this->iso3 = $iso3;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIso2(): string
    {
        return $this->iso2;
    }

    /**
     * @param mixed $iso2
     */
    public function setIso2(?string $iso2): self
    {
        $this->iso2 = $iso2;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCapital():string
    {
        return $this->capital;
    }

    /**
     * @param mixed $capital
     */
    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency():string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     * @return MstCountry
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->country;
    }

}
