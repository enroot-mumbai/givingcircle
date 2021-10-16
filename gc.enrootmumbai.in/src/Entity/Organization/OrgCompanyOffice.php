<?php

namespace App\Entity\Organization;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstOfficeCategory;
use App\Entity\Master\MstState;
use App\Entity\SystemApp\AppUserInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Organization\OrgCompanyOfficeRepository")
 * @ORM\Table("orgcompanyoffice")
 */
class OrgCompanyOffice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $office;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officeAddressOne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $officeAddressTwo;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $officePincode;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     * )
     */
    private $officeTelNumber;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $officeFaxNumber;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
       */
    private $officeEmail;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $officeTimeZone;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization\OrgCompany", inversedBy="orgCompanyOffice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstCountry")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCountry;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstState")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstCity")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstOfficeCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstOfficeCategory;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SystemApp\AppUserInfo", mappedBy="orgCompanyOffice")
     */
    private $appUserInfo;

    public function __construct()
    {
        $this->appUserInfo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(?string $office): self
    {
        $this->office = $office;

        return $this;
    }


    public function getOfficeAddressOne(): ?string
    {
        return $this->officeAddressOne;
    }

    public function setOfficeAddressOne(?string $officeAddressOne): self
    {
        $this->officeAddressOne = $officeAddressOne;

        return $this;
    }

    public function getOfficeAddressTwo(): ?string
    {
        return $this->officeAddressTwo;
    }

    public function setOfficeAddressTwo(?string $officeAddressTwo): self
    {
        $this->officeAddressTwo = $officeAddressTwo;

        return $this;
    }

    public function getOfficePincode(): ?string
    {
        return $this->officePincode;
    }

    public function setOfficePincode(?string $officePincode): self
    {
        $this->officePincode = $officePincode;

        return $this;
    }

     public function getOfficeTelNumber(): ?string
    {
        return $this->officeTelNumber;
    }

    public function setOfficeTelNumber(?string $officeTelNumber): self
    {
        $this->officeTelNumber = $officeTelNumber;

        return $this;
    }

    public function getOfficeFaxNumber(): ?string
    {
        return $this->officeFaxNumber;
    }

    public function setOfficeFaxNumber(?string $officeFaxNumber): self
    {
        $this->officeFaxNumber = $officeFaxNumber;

        return $this;
    }

    public function getOfficeEmail(): ?string
    {
        return $this->officeEmail;
    }

    public function setOfficeEmail(?string $officeEmail): self
    {
        $this->officeEmail = $officeEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOfficeTimeZone(): ?string
    {
        return $this->officeTimeZone;
    }
    /**
     * @param string|null $officeTimeZone
     * @return $this
     */
    public function setOfficeTimeZone(?string $officeTimeZone): self
    {
        $this->officeTimeZone = $officeTimeZone;
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

    public function getOrgCompany(): ?OrgCompany
    {
        return $this->orgCompany;
    }

    public function setOrgCompany(?OrgCompany $orgCompany): self
    {
        $this->orgCompany = $orgCompany;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->office;
    }


    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }

    public function getMstState(): ?MstState
    {
        return $this->mstState;
    }

    public function setMstState(?MstState $mstState): self
    {
        $this->mstState = $mstState;

        return $this;
    }

    public function getMstCity(): ?MstCity
    {
        return $this->mstCity;
    }

    public function setMstCity(?MstCity $mstCity): self
    {
        $this->mstCity = $mstCity;

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
            $appUserInfo->setOrgCompanyOffice($this);
        }

        return $this;
    }

    public function removeAppUserInfo(AppUserInfo $appUserInfo): self
    {
        if ($this->appUserInfo->contains($appUserInfo)) {
            $this->appUserInfo->removeElement($appUserInfo);
            // set the owning side to null (unless already changed)
            if ($appUserInfo->getOrgCompanyOffice() === $this) {
                $appUserInfo->setOrgCompanyOffice(null);
            }
        }

        return $this;
    }

}
