<?php

namespace App\Entity\SystemApp;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppModuleRepository")
 * @ORM\Table("appmodule")
 */
class AppModule
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
    private $moduleName;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $moduleValue;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SystemApp\AppUserCategory", mappedBy="appModule")
     */
    private $appUserCategory;

    /**
     * @ORM\ManyToMany(targetEntity=AppRole::class, mappedBy="appModule")
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

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function setModuleName(string $moduleName): self
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    public function getModuleValue(): ?string
    {
        return $this->moduleValue;
    }

    public function setModuleValue(string $moduleValue): self
    {
        $this->moduleValue = $moduleValue;

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
        return $this->getModuleName();
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
            $appUserCategory->addAppModule($this);
        }

        return $this;
    }

    public function removeAppUserCategory(AppUserCategory $appUserCategory): self
    {
        if ($this->appUserCategory->contains($appUserCategory)) {
            $this->appUserCategory->removeElement($appUserCategory);
            $appUserCategory->removeAppModule($this);
        }

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
            $appRole->addAppModule($this);
        }

        return $this;
    }

    public function removeAppRole(AppRole $appRole): self
    {
        if ($this->appRole->contains($appRole)) {
            $this->appRole->removeElement($appRole);
            $appRole->removeAppModule($this);
        }

        return $this;
    }

}
