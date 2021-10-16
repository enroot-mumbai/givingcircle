<?php

namespace App\Entity\SystemApp;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppUserCategoryRepository")
 * @ORM\Table("appusercategory")
 * @UniqueEntity(fields={"userCategory"}, message="user.userCategory.unique")
 */
class AppUserCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(message="User Category cannot be blank")
     */
    private $userCategory;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SystemApp\AppModule", inversedBy="appUserCategory")
     * @ORM\JoinTable(name="app_module_usertype")
     */
    private $appModule;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SystemApp\AppSubModule", inversedBy="appUserCategory")
     * @ORM\JoinTable(name="app_submodule_usertype")
     */
    private $appSubModule;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SystemApp\AppUser", mappedBy="appUserCategory")
     */
    private $appUser;


    public function __construct()
    {
        $this->appUser = new ArrayCollection();
        $this->appModule = new ArrayCollection();
        $this->appSubModule = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCategory(): ?string
    {
        return $this->userCategory;
    }

    public function setUserCategory(string $userCategory): self
    {
        $this->userCategory = $userCategory;

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
        return $this->userCategory;
    }

    /**
     * @return Collection|AppUser[]
     */
    public function getAppUser(): Collection
    {
        return $this->appUser;
    }

    public function addAppUser(AppUser $appUser): self
    {
        if (!$this->appUser->contains($appUser)) {
            $this->appUser[] = $appUser;
            $appUser->setAppUserCategory($this);
        }

        return $this;
    }

    public function removeAppUser(AppUser $appUser): self
    {
        if ($this->appUser->contains($appUser)) {
            $this->appUser->removeElement($appUser);
            // set the owning side to null (unless already changed)
            if ($appUser->getAppUserCategory() === $this) {
                $appUser->setAppUserCategory(null);
            }
        }

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
            $appModule->addAppUserCategory($this);
        }

        return $this;
    }

    public function removeAppModule(AppModule $appModule): self
    {
        if ($this->appModule->contains($appModule)) {
            $this->appModule->removeElement($appModule);
            $appModule->removeAppUserCategory($this);
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
            $appSubModule->addAppUserCategory($this);
        }

        return $this;
    }

    public function removeSubModuleUserCategory(AppSubModule $appSubModule): self
    {
        if ($this->appSubModule->contains($appSubModule)) {
            $this->appSubModule->removeElement($appSubModule);
            $appSubModule->removeAppUserCategory($this);
        }

        return $this;
    }
}
