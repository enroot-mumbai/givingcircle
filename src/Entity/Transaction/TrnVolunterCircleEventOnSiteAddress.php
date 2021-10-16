<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreasInCity;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventOnSiteAddressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventOnSiteAddressRepository::class)
 * @ORM\Table("trnvoluntercircleeventonsiteaddress")
 */
class TrnVolunterCircleEventOnSiteAddress
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterCircleEventDetails::class, inversedBy="trnVolunterCircleEventOnSiteAddresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $eventOnSiteAddress1;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $eventOnSiteAddress2;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $eventOnSitePinCode;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $eventOnSiteLocationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $eventOnSiteLocationLongitude;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreasInCity::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstAreasInCity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstCountry;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnVolunterCircleEventDetails(): ?TrnVolunterCircleEventDetails
    {
        return $this->trnVolunterCircleEventDetails;
    }

    public function setTrnVolunterCircleEventDetails(?TrnVolunterCircleEventDetails $trnVolunterCircleEventDetails): self
    {
        $this->trnVolunterCircleEventDetails = $trnVolunterCircleEventDetails;

        return $this;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): self
    {
        $this->appUser = $appUser;

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

    public function getEventOnSiteAddress1(): ?string
    {
        return $this->eventOnSiteAddress1;
    }

    public function setEventOnSiteAddress1(string $eventOnSiteAddress1): self
    {
        $this->eventOnSiteAddress1 = $eventOnSiteAddress1;

        return $this;
    }

    public function getEventOnSiteAddress2(): ?string
    {
        return $this->eventOnSiteAddress2;
    }

    public function setEventOnSiteAddress2(?string $eventOnSiteAddress2): self
    {
        $this->eventOnSiteAddress2 = $eventOnSiteAddress2;

        return $this;
    }

    public function getEventOnSitePinCode(): ?string
    {
        return $this->eventOnSitePinCode;
    }

    public function setEventOnSitePinCode(string $eventOnSitePinCode): self
    {
        $this->eventOnSitePinCode = $eventOnSitePinCode;

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

    public function getEventOnSiteLocationLatitude(): ?string
    {
        return $this->eventOnSiteLocationLatitude;
    }

    public function setEventOnSiteLocationLatitude(string $eventOnSiteLocationLatitude): self
    {
        $this->eventOnSiteLocationLatitude = $eventOnSiteLocationLatitude;

        return $this;
    }

    public function getEventOnSiteLocationLongitude(): ?string
    {
        return $this->eventOnSiteLocationLongitude;
    }

    public function setEventOnSiteLocationLongitude(string $eventOnSiteLocationLongitude): self
    {
        $this->eventOnSiteLocationLongitude = $eventOnSiteLocationLongitude;

        return $this;
    }

    public function getMstAreasInCity(): ?MstAreasInCity
    {
        return $this->mstAreasInCity;
    }

    public function setMstAreasInCity(?MstAreasInCity $mstAreasInCity): self
    {
        $this->mstAreasInCity = $mstAreasInCity;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

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
    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = null;
    }
}
