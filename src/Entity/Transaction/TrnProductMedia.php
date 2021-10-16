<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnProductMediaRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnProductMediaRepository::class)
 * @ORM\Table("trnproductmedia")
 */
class TrnProductMedia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstAreaInterestPrimary;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     */
    private $mstAreaInterestSecondary;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $mediaType;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mediaFileName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $mediaName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mediaAltText;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mediaTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mediaURL;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadedFilePath;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $uploadUserIpAddress;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnProductMedia")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnProductMedia")
     */
    private $trnCircleEvents;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMstAreaInterestPrimary(): ?MstAreaInterest
    {
        return $this->mstAreaInterestPrimary;
    }

    public function setMstAreaInterestPrimary(?MstAreaInterest $mstAreaInterestPrimary): self
    {
        $this->mstAreaInterestPrimary = $mstAreaInterestPrimary;

        return $this;
    }

    public function getMstAreaInterestSecondary(): ?MstAreaInterest
    {
        return $this->mstAreaInterestSecondary;
    }

    public function setMstAreaInterestSecondary(?MstAreaInterest $mstAreaInterestSecondary): self
    {
        $this->mstAreaInterestSecondary = $mstAreaInterestSecondary;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(?string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getMediaFileName(): ?string
    {
        return $this->mediaFileName;
    }

    public function setMediaFileName(?string $mediaFileName): self
    {
        $this->mediaFileName = $mediaFileName;

        return $this;
    }

    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }

    public function setMediaName(string $mediaName): self
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    public function getMediaAltText(): ?string
    {
        return $this->mediaAltText;
    }

    public function setMediaAltText(?string $mediaAltText): self
    {
        $this->mediaAltText = $mediaAltText;

        return $this;
    }

    public function getMediaTitle(): ?string
    {
        return $this->mediaTitle;
    }

    public function setMediaTitle(?string $mediaTitle): self
    {
        $this->mediaTitle = $mediaTitle;

        return $this;
    }

    public function getMediaURL(): ?string
    {
        return $this->mediaURL;
    }

    public function setMediaURL(?string $mediaURL): self
    {
        $this->mediaURL = $mediaURL;

        return $this;
    }

    public function getUploadedFilePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'. $this->getMediaFileName();
    }

    public function setUploadedFilePath(?string $uploadedFilePath): self
    {
        $this->uploadedFilePath = $uploadedFilePath;

        return $this;
    }

    public function getUploadUserIpAddress(): ?string
    {
        return $this->uploadUserIpAddress;
    }

    public function setUploadUserIpAddress(string $uploadUserIpAddress): self
    {
        $this->uploadUserIpAddress = $uploadUserIpAddress;

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

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getLocationLatitude(): ?string
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(?string $locationLatitude): self
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(?string $locationLongitude): self
    {
        $this->locationLongitude = $locationLongitude;

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

    public function getTrnCircle(): ?TrnCircle
    {
        return $this->trnCircle;
    }

    public function setTrnCircle(?TrnCircle $trnCircle): self
    {
        $this->trnCircle = $trnCircle;

        return $this;
    }

    public function getTrnCircleEvents(): ?TrnCircleEvents
    {
        return $this->trnCircleEvents;
    }

    public function setTrnCircleEvents(?TrnCircleEvents $trnCircleEvents): self
    {
        $this->trnCircleEvents = $trnCircleEvents;

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
