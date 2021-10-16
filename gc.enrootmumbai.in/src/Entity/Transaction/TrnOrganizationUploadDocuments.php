<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnOrganizationUploadDocumentsRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnOrganizationUploadDocumentsRepository::class)
 * @ORM\Table("trnorganizationuploaddocuments")
 */
class TrnOrganizationUploadDocuments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uploadDocumentPath;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstUploadDocumentType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstUploadDocumentType;

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
     * @ORM\Column(type="string", length=50)
     */
    private $userIpAddress;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class, inversedBy="trnOrganizationUploadDocuments")
     */
    private $appUserMemberDetails;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadDocumentPath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'. $this->getMediaFileName();
    }

    public function setUploadDocumentPath(string $uploadDocumentPath): self
    {
        $this->uploadDocumentPath = $uploadDocumentPath;

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

    public function getMstUploadDocumentType(): ?MstUploadDocumentType
    {
        return $this->mstUploadDocumentType;
    }

    public function setMstUploadDocumentType(?MstUploadDocumentType $mstUploadDocumentType): self
    {
        $this->mstUploadDocumentType = $mstUploadDocumentType;

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

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

        return $this;
    }

    public function getAppUserMemberDetails(): ?AppUser
    {
        return $this->appUserMemberDetails;
    }

    public function setAppUserMemberDetails(?AppUser $appUserMemberDetails): self
    {
        $this->appUserMemberDetails = $appUserMemberDetails;

        return $this;
    }
}
