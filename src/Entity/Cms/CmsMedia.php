<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstMediaCategory;
use App\Entity\Master\MstMediaSubCategory;
use App\Entity\Organization\OrgCompany;
use App\Repository\Cms\CmsMediaRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsMediaRepository::class)
 * @ORM\Table("cmsmedia", indexes={@ORM\Index(name="Name_Active_idx", columns={"mediaName", "isActive"})})
 */
class CmsMedia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=MstMediaCategory::class)
     */
    private $mediaCategory;

    /**
     * @ORM\ManyToOne(targetEntity=MstMediaSubCategory::class)
     */
    private $mediaSubCategory;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $mediaType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaName;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $mediaTag = [];

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $mediaPath;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $mediaImage;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mediaImagePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaDescription;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sequenceNo;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaCategory(): ?MstMediaCategory
    {
        return $this->mediaCategory;
    }

    public function setMediaCategory(?MstMediaCategory $mediaCategory): self
    {
        $this->mediaCategory = $mediaCategory;

        return $this;
    }

    public function getMediaSubCategory(): ?MstMediaSubCategory
    {
        return $this->mediaSubCategory;
    }

    public function setMediaSubCategory(?MstMediaSubCategory $mediaSubCategory): self
    {
        $this->mediaSubCategory = $mediaSubCategory;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): self
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }

    public function setMediaName(?string $mediaName): self
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    public function getMediaTag(): ?array
    {
        return $this->mediaTag;
    }

    public function setMediaTag(?array $mediaTag): self
    {
        $this->mediaTag = $mediaTag;

        return $this;
    }

    public function getMediaPath(): ?string
    {
        return $this->mediaPath;
    }

    public function setMediaPath(?string $mediaPath): self
    {
        $this->mediaPath = $mediaPath;

        return $this;
    }

    public function getMediaImage(): ?string
    {
        return $this->mediaImage;
    }

    public function setMediaImage(string $mediaImage): self
    {
        $this->mediaImage = $mediaImage;

        return $this;
    }

    public function getMediaImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getMediaImage();
    }

    public function setMediaImagePath(string $mediaImagePath): self
    {
        $this->mediaImagePath = $mediaImagePath;

        return $this;
    }

    public function getMediaDescription(): ?string
    {
        return $this->mediaDescription;
    }

    public function setMediaDescription(?string $mediaDescription): self
    {
        $this->mediaDescription = $mediaDescription;

        return $this;
    }

    public function getSequenceNo(): ?int
    {
        return $this->sequenceNo;
    }

    public function setSequenceNo(int $sequenceNo): self
    {
        $this->sequenceNo = $sequenceNo;

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


}
