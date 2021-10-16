<?php

namespace App\Entity\Master;

use App\Repository\Master\MstSkillSetRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MstSkillSetRepository::class)
 * @ORM\Table("mstskillset")
 * @UniqueEntity(fields={"skillSet"}, message="The value is already in the system")
 */
class MstSkillSet
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
    private $skillSet;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDefault;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iconWhite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSkillSet(): ?string
    {
        return $this->skillSet;
    }

    public function setSkillSet(string $skillSet): self
    {
        $this->skillSet = $skillSet;

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

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->skillSet;
    }

    public function getIcon(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIconWhite(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->iconWhite;
    }

    public function setIconWhite(?string $iconWhite): self
    {
        $this->iconWhite = $iconWhite;

        return $this;
    }
}
