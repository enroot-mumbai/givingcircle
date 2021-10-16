<?php

namespace App\Entity\Master;

use App\Repository\Master\MstArticleCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MstArticleCategoryRepository::class)
 * @ORM\Table("mstarticlecategory")
 * @UniqueEntity(fields={"articleCategory"}, message="The value is already in the system")
 */
class MstArticleCategory
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
    private $articleCategory;

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

    public function getArticleCategory(): ?string
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(string $articleCategory): self
    {
        $this->articleCategory = $articleCategory;

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

    public function __toString()
    {
        return $this->articleCategory;
    }
}
