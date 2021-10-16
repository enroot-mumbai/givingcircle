<?php

namespace App\Entity\Master;

use App\Repository\Master\MstMediaSubCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MstMediaSubCategoryRepository::class)
 * @ORM\Table("mstmediasubcategory")
 */
class MstMediaSubCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MstMediaCategory::class, inversedBy="mstMediaSubCategory")
     */
    private $mediaCategory;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mediaSubCategory;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sequenceNo;

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

    public function getMediaCategory(): ?MstMediaCategory
    {
        return $this->mediaCategory;
    }

    public function setMediaCategory(?MstMediaCategory $mediaCategory): self
    {
        $this->mediaCategory = $mediaCategory;

        return $this;
    }

    public function getMediaSubCategory(): ?string
    {
        return $this->mediaSubCategory;
    }

    public function setMediaSubCategory(string $mediaSubCategory): self
    {
        $this->mediaSubCategory = $mediaSubCategory;
        return $this;
    }

    public function getSequenceNo(): ?int
    {
        return $this->sequenceNo;
    }

    public function setSequenceNo(int $sequenceNo): self
    {
        $this->sequenceNo = $sequenceNo;
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
        return $this->mediaSubCategory;
    }
}
