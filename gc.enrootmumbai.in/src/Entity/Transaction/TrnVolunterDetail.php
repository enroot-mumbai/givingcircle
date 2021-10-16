<?php

namespace App\Entity\Transaction;
use App\Entity\Master\MstEmploymentStatus;
use App\Entity\Master\MstSourceOfInformation;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Master\MstGender;
use App\Entity\Master\MstMaritalStatus;

use App\Repository\Transaction\TrnVolunterDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterDetailRepository::class)
 * @ORM\Table("trnvolunterdetail")
 */
class TrnVolunterDetail
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
    private $educationLevel;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $distanceWillingToTravel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasDisability;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWillingToHelpInDisaster;

    /**
     * @ORM\ManyToOne(targetEntity=MstGender::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstGender;

    /**
     * @ORM\ManyToOne(targetEntity=MstMaritalStatus::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstMaritalStatus;

    /**
     * @ORM\ManyToMany(targetEntity=TrnVolunterAvailability::class, mappedBy="trnVolunterDetail")
     */
    private $trnVolunterAvailabilities;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=MstEmploymentStatus::class)
     */
    private $mstEmploymentStatus;

    /**
     * @ORM\ManyToMany(targetEntity=MstSourceOfInformation::class)
     */
    private $mstSourceOfInformation;

    /**
     * @ORM\OneToOne(targetEntity=AppUser::class, inversedBy="trnVolunterDetail", cascade={"persist", "remove"})
     */
    private $appUser;

    /**
     * @ORM\OneToMany(targetEntity=TrnVolunteerDocument::class, mappedBy="trnVolunterDetail")
     */
    private $trnVolunteerDocuments;

    public function __construct()
    {
        $this->trnVolunterAvailabilities = new ArrayCollection();
        $this->mstSourceOfInformation = new ArrayCollection();
        $this->trnVolunteerDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEducationLevel(): ?string
    {
        return $this->educationLevel;
    }

    public function setEducationLevel(?string $educationLevel): self
    {
        $this->educationLevel = $educationLevel;

        return $this;
    }

    public function getDistanceWillingToTravel(): ?string
    {
        return $this->distanceWillingToTravel;
    }

    public function setDistanceWillingToTravel(?string $distanceWillingToTravel): self
    {
        $this->distanceWillingToTravel = $distanceWillingToTravel;

        return $this;
    }

    public function getHasDisability(): ?bool
    {
        return $this->hasDisability;
    }

    public function setHasDisability(bool $hasDisability): self
    {
        $this->hasDisability = $hasDisability;

        return $this;
    }

    public function getIsWillingToHelpInDisaster(): ?bool
    {
        return $this->isWillingToHelpInDisaster;
    }

    public function setIsWillingToHelpInDisaster(bool $isWillingToHelpInDisaster): self
    {
        $this->isWillingToHelpInDisaster = $isWillingToHelpInDisaster;

        return $this;
    }

    public function getMstGender(): ?MstGender
    {
        return $this->mstGender;
    }

    public function setMstGender(?MstGender $mstGender): self
    {
        $this->mstGender = $mstGender;

        return $this;
    }

    public function getMstMaritalStatus(): ?MstMaritalStatus
    {
        return $this->mstMaritalStatus;
    }

    public function setMstMaritalStatus(?MstMaritalStatus $mstMaritalStatus): self
    {
        $this->mstMaritalStatus = $mstMaritalStatus;

        return $this;
    }
    
    /**
     * @return Collection|TrnVolunterAvailability[]
     */
    public function getTrnVolunterAvailabilities(): Collection
    {
        return $this->trnVolunterAvailabilities;
    }

    public function addTrnVolunterAvailability(TrnVolunterAvailability $trnVolunterAvailability): self
    {
        if (!$this->trnVolunterAvailabilities->contains($trnVolunterAvailability)) {
            $this->trnVolunterAvailabilities[] = $trnVolunterAvailability;
            $trnVolunterAvailability->addTrnVolunterDetail($this);
        }

        return $this;
    }

    public function removeTrnVolunterAvailability(TrnVolunterAvailability $trnVolunterAvailability): self
    {
        if ($this->trnVolunterAvailabilities->contains($trnVolunterAvailability)) {
            $this->trnVolunterAvailabilities->removeElement($trnVolunterAvailability);
            $trnVolunterAvailability->removeTrnVolunterDetail($this);
        }

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

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }

    public function getLocationLatitude(): ?string
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(string $locationLatitude): self
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(string $locationLongitude): self
    {
        $this->locationLongitude = $locationLongitude;

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
    
    public function getMstEmploymentStatus(): ?MstEmploymentStatus
    {
        return $this->mstEmploymentStatus;
    }

    public function setMstEmploymentStatus(?MstEmploymentStatus $mstEmploymentStatus): self
    {
        $this->mstEmploymentStatus = $mstEmploymentStatus;

        return $this;
    }

    /**
     * @return Collection|MstSourceOfInformation[]
     */
    public function getMstSourceOfInformation(): Collection
    {
        return $this->mstSourceOfInformation;
    }

    public function addMstSourceOfInformation(MstSourceOfInformation $mstSourceOfInformation): self
    {
        if (!$this->mstSourceOfInformation->contains($mstSourceOfInformation)) {
            $this->mstSourceOfInformation[] = $mstSourceOfInformation;
        }

        return $this;
    }

    public function removeMstSourceOfInformation(MstSourceOfInformation $mstSourceOfInformation): self
    {
        $this->mstSourceOfInformation->removeElement($mstSourceOfInformation);

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

    /**
     * @return Collection|TrnVolunteerDocument[]
     */
    public function getTrnVolunteerDocuments(): Collection
    {
        return $this->trnVolunteerDocuments;
    }

    public function addTrnVolunteerDocument(TrnVolunteerDocument $trnVolunteerDocument): self
    {
        if (!$this->trnVolunteerDocuments->contains($trnVolunteerDocument)) {
            $this->trnVolunteerDocuments[] = $trnVolunteerDocument;
            $trnVolunteerDocument->setTrnVolunterDetail($this);
        }

        return $this;
    }

    public function removeTrnVolunteerDocument(TrnVolunteerDocument $trnVolunteerDocument): self
    {
        if ($this->trnVolunteerDocuments->removeElement($trnVolunteerDocument)) {
            // set the owning side to null (unless already changed)
            if ($trnVolunteerDocument->getTrnVolunterDetail() === $this) {
                $trnVolunteerDocument->setTrnVolunterDetail(null);
            }
        }

        return $this;
    }
}
