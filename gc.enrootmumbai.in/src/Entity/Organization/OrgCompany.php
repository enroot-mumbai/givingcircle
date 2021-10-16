<?php

namespace App\Entity\Organization;

use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstOfficeCategory;
use App\Entity\SystemApp\AppUserInfo;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Service\FileUploaderHelper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Organization\OrgCompanyRepository")
 * @ORM\Table("orgcompany")
 */
class OrgCompany
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $companyId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *     message="Company Name cannot be blank"
     * )
     * @Assert\Length(
     *     min = 3,
     *     minMessage="Company name must be at least {{ limit }} characters long"
     *     )
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $companyLogo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $companyFiscalStartMonth;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $companyFiscalEndMonth;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $companyLogoFilePath;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Organization\OrgCompanyOffice", mappedBy="orgCompany")
     */
    private $orgCompanyOffice;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $companyWebsite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstOfficeCategory")
     */
    private $mstOfficeCategory;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstCurrency")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCurrency;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SystemApp\AppUserInfo", mappedBy="orgCompany")
     */
    private $appUserInfo;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->orgCompanyOffice = new ArrayCollection();
        $this->appUserInfo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function setCompanyId(?string $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }



    public function getCompanyFiscalStartMonth(): ?DateTimeInterface
    {
        return $this->companyFiscalStartMonth;
    }

    public function setCompanyFiscalStartMonth(?DateTimeInterface $companyFiscalStartMonth): self
    {
        $this->companyFiscalStartMonth = $companyFiscalStartMonth;

        return $this;
    }

    public function getCompanyFiscalEndMonth(): ?DateTimeInterface
    {
        return $this->companyFiscalEndMonth;
    }

    public function setCompanyFiscalEndMonth(?DateTimeInterface $companyFiscalEndMonth): self
    {
        $this->companyFiscalEndMonth = $companyFiscalEndMonth;

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

    public function getCompanyLogo(): ?string
    {
        return $this->companyLogo;
    }

    public function setCompanyLogo(?string $companyLogo): self
    {
        $this->companyLogo = $companyLogo;

        return $this;
    }

    public function getCompanyLogoFilePath(): ?string
    {
       // return $this->companyLogoFilePath;
        return FileUploaderHelper::PRIVATE_FILE.'/'.$this->getCompanyLogo();
    }

    public function setCompanyLogoFilePath(?string $companyLogoFilePath): self
    {
        $this->companyLogoFilePath = $companyLogoFilePath;

        return $this;
    }

    public function getCompanyWebsite(): ?string
    {
        return $this->companyWebsite;
    }

    public function setCompanyWebsite(?string $companyWebsite): self
    {
        $this->companyWebsite = $companyWebsite;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->company;
    }

    /**
     * @return Collection|OrgCompanyOffice[]
     */
    public function getOrgCompanyOffice(): Collection
    {
        return $this->orgCompanyOffice;
    }

    public function addOrgCompanyOffice(OrgCompanyOffice $orgCompanyOffice): self
    {
        if (!$this->orgCompanyOffice->contains($orgCompanyOffice)) {
            $this->orgCompanyOffice[] = $orgCompanyOffice;
            $orgCompanyOffice->setOrgCompany($this);
        }

        return $this;
    }

    public function removeOrgCompanyOffice(OrgCompanyOffice $orgCompanyOffice): self
    {
        if ($this->orgCompanyOffice->contains($orgCompanyOffice)) {
            $this->orgCompanyOffice->removeElement($orgCompanyOffice);
            // set the owning side to null (unless already changed)
            if ($orgCompanyOffice->getOrgCompany() === $this) {
                $orgCompanyOffice->setOrgCompany(null);
            }
        }

        return $this;
    }

    public function getMstOfficeCategory(): ?MstOfficeCategory
    {
        return $this->mstOfficeCategory;
    }

    public function setMstOfficeCategory(?MstOfficeCategory $mstOfficeCategory): self
    {
        $this->mstOfficeCategory = $mstOfficeCategory;

        return $this;
    }

    public function getMstCurrency(): ?MstCurrency
    {
        return $this->mstCurrency;
    }

    public function setMstCurrency(?MstCurrency $mstCurrency): self
    {
        $this->mstCurrency = $mstCurrency;

        return $this;
    }

    /**
     * @return Collection|AppUserInfo[]
     */
    public function getAppUserInfo(): Collection
    {
        return $this->appUserInfo;
    }

    public function addAppUserInfo(AppUserInfo $appUserInfo): self
    {
        if (!$this->appUserInfo->contains($appUserInfo)) {
            $this->appUserInfo[] = $appUserInfo;
            $appUserInfo->setOrgCompany($this);
        }

        return $this;
    }

    public function removeAppUserInfo(AppUserInfo $appUserInfo): self
    {
        if ($this->appUserInfo->contains($appUserInfo)) {
            $this->appUserInfo->removeElement($appUserInfo);
            // set the owning side to null (unless already changed)
            if ($appUserInfo->getOrgCompany() === $this) {
                $appUserInfo->setOrgCompany(null);
            }
        }

        return $this;
    }

}
