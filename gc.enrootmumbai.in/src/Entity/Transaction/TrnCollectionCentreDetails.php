<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCollectionCentreDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCollectionCentreDetailsRepository::class)
 * @ORM\Table("trncollectioncentredetails")
 */
class TrnCollectionCentreDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $pinCode;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstStatus;

    /**
     * @ORM\OneToMany(targetEntity=TrnMaterialReceivedAtCollectionCentre::class, mappedBy="trnCollectionCentreDetails", orphanRemoval=true)
     */
    private $trnMaterialReceivedAtCollectionCentres;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCountry;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $startTime;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $endTime;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $modeOfInsertion;

    /**
     * @ORM\ManyToMany(targetEntity=MstDaysOfWeek::class)
     */
    private $mstDaysOfWeek;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCollectionCentreDetails")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCollectionCentreDetails")
     */
    private $trnCircleEvent;

    public function __construct()
    {
        $this->trnMaterialReceivedAtCollectionCentres = new ArrayCollection();
        $this->mstDaysOfWeek = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPinCode(): ?string
    {
        return $this->pinCode;
    }

    public function setPinCode(?string $pinCode): self
    {
        $this->pinCode = $pinCode;

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

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

        return $this;
    }

    /**
     * @return Collection|TrnMaterialReceivedAtCollectionCentre[]
     */
    public function getTrnMaterialReceivedAtCollectionCentres(): Collection
    {
        return $this->trnMaterialReceivedAtCollectionCentres;
    }

    public function addTrnMaterialReceivedAtCollectionCentre(TrnMaterialReceivedAtCollectionCentre $trnMaterialReceivedAtCollectionCentre): self
    {
        if (!$this->trnMaterialReceivedAtCollectionCentres->contains($trnMaterialReceivedAtCollectionCentre)) {
            $this->trnMaterialReceivedAtCollectionCentres[] = $trnMaterialReceivedAtCollectionCentre;
            $trnMaterialReceivedAtCollectionCentre->setTrnCollectionCentreDetails($this);
        }

        return $this;
    }

    public function removeTrnMaterialReceivedAtCollectionCentre(TrnMaterialReceivedAtCollectionCentre $trnMaterialReceivedAtCollectionCentre): self
    {
        if ($this->trnMaterialReceivedAtCollectionCentres->contains($trnMaterialReceivedAtCollectionCentre)) {
            $this->trnMaterialReceivedAtCollectionCentres->removeElement($trnMaterialReceivedAtCollectionCentre);
            // set the owning side to null (unless already changed)
            if ($trnMaterialReceivedAtCollectionCentre->getTrnCollectionCentreDetails() === $this) {
                $trnMaterialReceivedAtCollectionCentre->setTrnCollectionCentreDetails(null);
            }
        }

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

    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getModeOfInsertion(): ?string
    {
        return $this->modeOfInsertion;
    }

    public function setModeOfInsertion(string $modeOfInsertion): self
    {
        $this->modeOfInsertion = $modeOfInsertion;

        return $this;
    }

    /**
     * @return Collection|MstDaysOfWeek[]
     */
    public function getMstDaysOfWeek(): Collection
    {
        return $this->mstDaysOfWeek;
    }

    public function addMstDaysOfWeek(MstDaysOfWeek $mstDaysOfWeek): self
    {
        if (!$this->mstDaysOfWeek->contains($mstDaysOfWeek)) {
            $this->mstDaysOfWeek[] = $mstDaysOfWeek;
        }

        return $this;
    }

    public function removeMstDaysOfWeek(MstDaysOfWeek $mstDaysOfWeek): self
    {
        if ($this->mstDaysOfWeek->contains($mstDaysOfWeek)) {
            $this->mstDaysOfWeek->removeElement($mstDaysOfWeek);
        }

        return $this;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return '<b>Name</b> :- '.$this->firstName. ' '.$this->lastName. ' <b>Address</b>:- '.$this->address1. ', '
            .$this->address2.
            ', '
            .$this->mstCity->getCity(). ', '.$this->mstState->getState(). ', '.$this->mstCountry->getCountry(). ', '
            .$this->pinCode;
    }

    public function getTrnCircle(): ?TrnCircle
    {
        return $this->trnCircle;
    }

    public function setTrnCircle(?TrnCircle $trnCircle): self
    {
        $this->trnCircle = $trnCircle;

        return $this;
    }

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

        return $this;
    }
}
