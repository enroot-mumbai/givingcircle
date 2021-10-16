<?php

namespace App\Entity\Cms;

use App\Service\FileUploaderHelper;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsBannerRepository")
 * @ORM\Table("cmsbanner")
 */
class CmsBanner
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CmsPage::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmsPage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bannerName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bannerDescription;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bannerUrl;

    /**
     * @ORM\Column(type="date")
     */
    private $bannerValidFromDate;

    /**
     * @ORM\Column(type="date")
     */
    private $bannerValidToDate;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $bannerMediaType;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private ?string $bannerPosition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerDesktopImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerTabletImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerMobileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerImageSetName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerImageAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerImageTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bannerDesktopImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bannerTabletImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bannerMobileImagePath;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $bannerVideo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bannerVideoPath;

    /**
     * @ORM\Column(type="smallint", nullable=true)
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


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmsPage(): ?CmsPage
    {
        return $this->cmsPage;
    }

    public function setCmsPage(?CmsPage $cmsPage): self
    {
        $this->cmsPage = $cmsPage;

        return $this;
    }

    public function getBannerName(): ?string
    {
        return $this->bannerName;
    }

    public function setBannerName(string $bannerName): self
    {
        $this->bannerName = $bannerName;

        return $this;
    }

    public function getBannerDescription(): ?string
    {
        return $this->bannerDescription;
    }

    public function setBannerDescription(?string $bannerDescription): self
    {
        $this->bannerDescription = $bannerDescription;

        return $this;
    }

    public function getBannerUrl(): ?string
    {
        return $this->bannerUrl;
    }

    public function setBannerUrl(?string $bannerUrl): self
    {
        $this->bannerUrl = $bannerUrl;

        return $this;
    }

    public function getBannerValidFromDate(): ?DateTimeInterface
    {
        return $this->bannerValidFromDate;
    }

    public function setBannerValidFromDate(DateTimeInterface $bannerValidFromDate): self
    {
        $this->bannerValidFromDate = $bannerValidFromDate;

        return $this;
    }

    public function getBannerValidToDate(): ?DateTimeInterface
    {
        return $this->bannerValidToDate;
    }

    public function setBannerValidToDate(DateTimeInterface $bannerValidToDate): self
    {
        $this->bannerValidToDate = $bannerValidToDate;

        return $this;
    }

    public function getBannerMediaType(): ?string
    {
        return $this->bannerMediaType;
    }

    public function setBannerMediaType(string $bannerMediaType): self
    {
        $this->bannerMediaType = $bannerMediaType;

        return $this;
    }

    public function getBannerPosition(): ?string
    {
        return $this->bannerPosition;
    }

    public function setBannerPosition(?string $bannerPosition): self
    {
        $this->bannerPosition = $bannerPosition;

        return $this;
    }

    public function getBannerDesktopImage(): ?string
    {
        return $this->bannerDesktopImage;
    }

    public function setBannerDesktopImage(string $bannerDesktopImage): self
    {
        $this->bannerDesktopImage = $bannerDesktopImage;

        return $this;
    }

    public function getBannerTabletImage(): ?string
    {
        return $this->bannerTabletImage;
    }

    public function setBannerTabletImage(string $bannerTabletImage): self
    {
        $this->bannerTabletImage = $bannerTabletImage;

        return $this;
    }

    public function getBannerMobileImage(): ?string
    {
        return $this->bannerMobileImage;
    }

    public function setBannerMobileImage(string $bannerMobileImage): self
    {
        $this->bannerMobileImage = $bannerMobileImage;

        return $this;
    }

    public function getBannerImageSetName(): ?string
    {
        return $this->bannerImageSetName;
    }

    public function setBannerImageSetName(string $bannerImageSetName): self
    {
        $this->bannerImageSetName = $bannerImageSetName;

        return $this;
    }

    public function getBannerImageAlt(): ?string
    {
        return $this->bannerImageAlt;
    }

    public function setBannerImageAlt(string $bannerImageAlt): self
    {
        $this->bannerImageAlt = $bannerImageAlt;

        return $this;
    }

    public function getBannerImageTitle(): ?string
    {
        return $this->bannerImageTitle;
    }

    public function setBannerImageTitle(string $bannerImageTitle): self
    {
        $this->bannerImageTitle = $bannerImageTitle;

        return $this;
    }

    public function getBannerDesktopImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getBannerDesktopImage();
    }

    public function setBannerDesktopImagePath(string $bannerDesktopImagePath): self
    {
        $this->bannerDesktopImagePath = $bannerDesktopImagePath;

        return $this;
    }

    public function getBannerTabletImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getBannerTabletImage();
    }

    public function setBannerTabletImagePath(string $bannerTabletImagePath): self
    {
        $this->bannerTabletImagePath = $bannerTabletImagePath;

        return $this;
    }

    public function getBannerMobileImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getBannerMobileImage();
    }

    public function setBannerMobileImagePath(string $bannerMobileImagePath): self
    {
        $this->bannerMobileImagePath = $bannerMobileImagePath;

        return $this;
    }

    public function getBannerVideo(): ?string
    {
        return $this->bannerVideo;
    }

    public function setBannerVideo(?string $bannerVideo): self
    {
        $this->bannerVideo = $bannerVideo;
        return $this;
    }

    public function getBannerVideoPath(): ?string
    {
        return $this->bannerVideoPath;
    }

    public function setBannerVideoPath(?string $bannerVideoPath): self
    {
        $this->bannerVideoPath = $bannerVideoPath;
        return $this;
    }

    public function getSequenceNo(): ?int
    {
        return $this->sequenceNo;
    }

    public function setSequenceNo(?int $sequenceNo): self
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


}
