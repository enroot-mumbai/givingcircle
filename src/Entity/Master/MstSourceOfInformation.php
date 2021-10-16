<?php

namespace App\Entity\Master;

use App\Repository\Master\MstSourceOfInformationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MstSourceOfInformationRepository::class)
 * @ORM\Table("mstsourceofinformation")
 * @UniqueEntity(fields={"sourceOfInformation"}, message="The value is already in the system")
 */
class MstSourceOfInformation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     */
    private $sourceOfInformation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceOfInformation(): ?string
    {
        return $this->sourceOfInformation;
    }

    public function setSourceOfInformation(string $sourceOfInformation): self
    {
        $this->sourceOfInformation = $sourceOfInformation;

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

    public function getRowId(): ?string
    {
        return $this->rowId;
    }

    public function setRowId(string $rowId): self
    {
        $this->rowId = $rowId;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->sourceOfInformation;
    }
}
