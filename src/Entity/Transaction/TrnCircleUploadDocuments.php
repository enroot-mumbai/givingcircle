<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstUploadDocumentType;
use App\Repository\Transaction\TrnCircleUploadDocumentsRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleUploadDocumentsRepository::class)
 * @ORM\Table("trncircleuploaddocuments")
 */
class TrnCircleUploadDocuments
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
    private $uploadedFilePath;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleUploadDocuments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnCircle;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $uploadUserIpAddress;

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
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLongitude;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEventBroadCastDetails::class, inversedBy="trnCircleUploadDocuments")
     */
    private $trnCircleEventBroadCast;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadedFilePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->uploadedFilePath;
    }

    public function setUploadedFilePath(string $uploadedFilePath): self
    {
        $this->uploadedFilePath = $uploadedFilePath;

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

    public function getMstUploadDocumentType(): ?MstUploadDocumentType
    {
        return $this->mstUploadDocumentType;
    }

    public function setMstUploadDocumentType(?MstUploadDocumentType $mstUploadDocumentType): self
    {
        $this->mstUploadDocumentType = $mstUploadDocumentType;

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

    public function getTrnCircleEventBroadCast(): ?TrnCircleEventBroadCastDetails
    {
        return $this->trnCircleEventBroadCast;
    }

    public function setTrnCircleEventBroadCast(?TrnCircleEventBroadCastDetails $trnCircleEventBroadCast): self
    {
        $this->trnCircleEventBroadCast = $trnCircleEventBroadCast;

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
}
