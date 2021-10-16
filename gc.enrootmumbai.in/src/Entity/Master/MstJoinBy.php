<?php

namespace App\Entity\Master;

use App\Repository\Master\MstJoinByRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MstJoinByRepository::class)
 * @ORM\Table("mstjoinby")
 * @UniqueEntity(fields={"joinBy"}, message="The value is already in the system")
 */
class MstJoinBy
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
    private $joinBy;

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

    public function getJoinBy(): ?string
    {
        return $this->joinBy;
    }

    public function setJoinBy(string $joinBy): self
    {
        $this->joinBy = $joinBy;

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
        return $this->joinBy;
    }
}
