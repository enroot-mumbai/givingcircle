<?php

namespace App\Entity\SystemApp;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppRoleRepository")
 * @ORM\Table("approle")
 */
class AppRole
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
    private $roleName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roleDescription;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isOrgEnabled;

    /**
     * @ORM\ManyToMany(targetEntity=AppModule::class, inversedBy="appRole")
     * @ORM\JoinTable(name="approle_appmodule")
     */
    private $appModule;

    /**
     * @ORM\ManyToMany(targetEntity=AppSubModule::class, inversedBy="appRole")
     * @ORM\JoinTable(name="approle_appsubmodule")
     */
    private $appSubModule;

    public function __construct()
    {
        $this->appModule = new ArrayCollection();
        $this->appSubModule = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }

    public function getRoleDescription(): ?string
    {
        return $this->roleDescription;
    }

    public function setRoleDescription(?string $roleDescription): self
    {
        $this->roleDescription = $roleDescription;

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
        return $this->roleName;
    }

    public function getIsOrgEnabled(): ?bool
    {
        return $this->isOrgEnabled;
    }

    public function setIsOrgEnabled(?bool $isOrgEnabled): self
    {
        $this->isOrgEnabled = $isOrgEnabled;

        return $this;
    }

    /**
     * @return Collection|AppModule[]
     */
    public function getAppModule(): Collection
    {
        return $this->appModule;
    }

    public function addAppModule(AppModule $appModule): self
    {
        if (!$this->appModule->contains($appModule)) {
            $this->appModule[] = $appModule;
            $appModule->addAppRole($this);
        }

        return $this;
    }

    public function removeAppModule(AppModule $appModule): self
    {
        if ($this->appModule->contains($appModule)) {
            $this->appModule->removeElement($appModule);
            $appModule->removeAppRole($this);
        }

        return $this;
    }

    /**
     * @return Collection|AppSubModule[]
     */
    public function getAppSubModule(): Collection
    {
        return $this->appSubModule;
    }

    public function addAppSubModule(AppSubModule $appSubModule): self
    {
        if (!$this->appSubModule->contains($appSubModule)) {
            $this->appSubModule[] = $appSubModule;
            $appSubModule->addAppRole($this);
        }

        return $this;
    }

    public function removeAppSubModule(AppSubModule $appSubModule): self
    {
        if ($this->appSubModule->contains($appSubModule)) {
            $this->appSubModule->removeElement($appSubModule);
            $appSubModule->removeAppRole($this);
        }

        return $this;
    }
}
