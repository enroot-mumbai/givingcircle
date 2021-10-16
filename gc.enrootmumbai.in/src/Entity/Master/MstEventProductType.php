<?php

namespace App\Entity\Master;

use App\Repository\Master\MstEventProductTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MstEventProductTypeRepository::class)
 * @ORM\Table("msteventproducttype")
 * @UniqueEntity(fields={"eventProductType"}, message="The value is already in the system")
 */
class MstEventProductType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank
     */
    private $eventProductType;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventProductType(): ?string
    {
        return $this->eventProductType;
    }

    public function setEventProductType(string $eventProductType): self
    {
        $this->eventProductType = $eventProductType;

        return $this;
    }

    public function getRowId(): ?string
    {
        return $this->rowId;
    }

    public function setRowId(string $rowId): self
    {
        $this->rowId = $rowId;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->eventProductType;
    }
}
