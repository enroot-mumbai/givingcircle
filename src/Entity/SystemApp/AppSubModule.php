<?php

namespace App\Entity\SystemApp;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppSubModuleRepository")
 * @ORM\Table("appsubmodule")
 */
class AppSubModule
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
    private $subModuleName;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $subModuleValue;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $subModuleParentValue;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subModuleStatic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subModuleDisplayMenu;

    /**
     * @ORM\Column(type="integer")
     */
    private $parentId;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\SystemApp\AppModule")
     * @ORM\JoinColumn(nullable=false)
     */
    private $appmodule;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SystemApp\AppUserCategory", mappedBy="appSubModule")
     */
    private $appUserCategory;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isChildMenu;

    /**
     * @ORM\ManyToMany(targetEntity=AppRole::class, mappedBy="appSubModule")
     */
    private $appRole;

    public function __construct()
    {
        $this->appUserCategory = new ArrayCollection();
        $this->appRole = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubModuleName(): ?string
    {
        return $this->subModuleName;
    }

    public function setSubModuleName(string $subModuleName): self
    {
        $this->subModuleName = $subModuleName;

        return $this;
    }

    public function getSubModuleValue(): ?string
    {
        return $this->subModuleValue;
    }

    public function setSubModuleValue(string $subModuleValue): self
    {
        $this->subModuleValue = $subModuleValue;

        return $this;
    }

    public function getSubModuleParentValue(): ?string
    {
        return $this->subModuleParentValue;
    }

    public function setSubModuleParentValue(?string $subModuleParentValue): self
    {
        $this->subModuleParentValue = $subModuleParentValue;

        return $this;
    }

    public function getSubModuleStatic(): ?bool
    {
        return $this->subModuleStatic;
    }

    public function setSubModuleStatic(bool $subModuleStatic): self
    {
        $this->subModuleStatic = $subModuleStatic;

        return $this;
    }

    public function getSubModuleDisplayMenu(): ?bool
    {
        return $this->subModuleDisplayMenu;
    }

    public function setSubModuleDisplayMenu(bool $subModuleDisplayMenu): self
    {
        $this->subModuleDisplayMenu = $subModuleDisplayMenu;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

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
        // TODO: Implement __toString() method.
        return $this->getSubModuleName();
    }

    public function getAppModule(): ?AppModule
    {
        return $this->appmodule;
    }

    public function setAppModule(?AppModule $appModule): self
    {
        $this->appmodule = $appModule;

        return $this;
    }

    /**
     * @return Collection|AppUserCategory[]
     */
    public function getAppUserCategory(): Collection
    {
        return $this->appUserCategory;
    }

    public function addAppUserCategory(AppUserCategory $appUserCategory): self
    {
        if (!$this->appUserCategory->contains($appUserCategory)) {
            $this->appUserCategory[] = $appUserCategory;
            $appUserCategory->addAppSubModule($this);
        }

        return $this;
    }

    public function removeAppUserCategory(AppUserCategory $appUserCategory): self
    {
        if ($this->appUserCategory->contains($appUserCategory)) {
            $this->appUserCategory->removeElement($appUserCategory);
            $appUserCategory->removeAppSubModule($this);
        }

        return $this;
    }

    public function getIsChildMenu(): ?bool
    {
        return $this->isChildMenu;
    }

    public function setIsChildMenu(bool $isChildMenu): self
    {
        $this->isChildMenu = $isChildMenu;

        return $this;
    }

    /**
     * @return Collection|AppRole[]
     */
    public function getAppRole(): Collection
    {
        return $this->appRole;
    }

    public function addAppRole(AppRole $appRole): self
    {
        if (!$this->appRole->contains($appRole)) {
            $this->appRole[] = $appRole;
            $appRole->addAppSubModule($this);
        }

        return $this;
    }

    public function removeAppRole(AppRole $appRole): self
    {
        if ($this->appRole->contains($appRole)) {
            $this->appRole->removeElement($appRole);
            $appRole->removeAppSubModule($this);
        }

        return $this;
    }
}
