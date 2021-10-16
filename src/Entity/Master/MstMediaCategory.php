<?php

namespace App\Entity\Master;

use App\Repository\Master\MstMediaCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MstMediaCategoryRepository::class)
 * @ORM\Table("mstmediacategory")
 * @UniqueEntity(fields={"mediaCategory"}, message="The value is already in the system")
 */
class MstMediaCategory
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
    private $mediaCategory;

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

    /**
     * @ORM\OneToMany(targetEntity=MstMediaSubCategory::class, mappedBy="mediaCategory")
     */
    private $mstMediaSubCategory;

    public function __construct()
    {
        $this->mstMediaSubCategory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaCategory(): ?string
    {
        return $this->mediaCategory;
    }

    public function setMediaCategory(string $mediaCategory): self
    {
        $this->mediaCategory = $mediaCategory;

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
        return $this->mediaCategory;
    }

    /**
     * @return Collection|MstMediaSubCategory[]
     */
    public function getMstMediaSubCategory(): Collection
    {
        return $this->mstMediaSubCategory;
    }

    public function addMstMediaSubCategory(MstMediaSubCategory $mstMediaSubCategory): self
    {
        if (!$this->mstMediaSubCategory->contains($mstMediaSubCategory)) {
            $this->mstMediaSubCategory[] = $mstMediaSubCategory;
            $mstMediaSubCategory->setMediaCategory($this);
        }

        return $this;
    }

    public function removeMstMediaSubCategory(MstMediaSubCategory $mstMediaSubCategory): self
    {
        if ($this->mstMediaSubCategory->contains($mstMediaSubCategory)) {
            $this->mstMediaSubCategory->removeElement($mstMediaSubCategory);
            // set the owning side to null (unless already changed)
            if ($mstMediaSubCategory->getMediaCategory() === $this) {
                $mstMediaSubCategory->setMediaCategory(null);
            }
        }

        return $this;
    }
}
