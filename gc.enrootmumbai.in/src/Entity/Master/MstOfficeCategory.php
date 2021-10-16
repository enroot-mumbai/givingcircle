<?php

namespace App\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Master\MstOfficeCategoryRepository")
 * @UniqueEntity(fields={"officeCategory"}, message="The value is already in the system")
 * @ORM\Table("mstofficecategory")
 */
class MstOfficeCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $officeCategory;

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

    public function getOfficeCategory(): ?string
    {
        return $this->officeCategory;
    }

    public function setOfficeCategory(string $officeCategory): self
    {
        $this->officeCategory = $officeCategory;

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
        // TODO: Implement __toString() method.
        return $this->officeCategory;
    }
}
