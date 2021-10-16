<?php

namespace App\Entity\Cms;

use App\Entity\Organization\OrgCompany;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsPressRoomRepository")
 * @ORM\Table("cmspressroom")
 */
class CmsPressRoom
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $articleDateTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $articleHeading;

    /**
     * @ORM\Column(type="text")
     */
    private $articleContent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroDesktopImage;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleIntroDesktopImagePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageSetName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageTitle;

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

    public function getArticleDateTime(): ?\DateTimeInterface
    {
        return $this->articleDateTime;
    }

    public function setArticleDateTime(\DateTimeInterface $articleDateTime): self
    {
        $this->articleDateTime = $articleDateTime;

        return $this;
    }

    public function getArticleHeading(): ?string
    {
        return $this->articleHeading;
    }

    public function setArticleHeading(string $articleHeading): self
    {
        $this->articleHeading = $articleHeading;

        return $this;
    }

    public function getArticleContent(): ?string
    {
        return $this->articleContent;
    }

    public function setArticleContent(string $articleContent): self
    {
        $this->articleContent = $articleContent;

        return $this;
    }

    public function getArticleIntroDesktopImage(): ?string
    {
        return $this->articleIntroDesktopImage;
    }

    public function setArticleIntroDesktopImage(?string $articleIntroDesktopImage): self
    {
        $this->articleIntroDesktopImage = $articleIntroDesktopImage;

        return $this;
    }

    public function getArticleIntroDesktopImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleIntroDesktopImage();
    }

    public function setArticleIntroDesktopImagePath(string $articleIntroDesktopImagePath): self
    {
        $this->articleIntroDesktopImagePath = $articleIntroDesktopImagePath;

        return $this;
    }

    public function getArticleIntroImageSetName(): ?string
    {
        return $this->articleIntroImageSetName;
    }

    public function setArticleIntroImageSetName(string $articleIntroImageSetName): self
    {
        $this->articleIntroImageSetName = $articleIntroImageSetName;

        return $this;
    }

    public function getArticleIntroImageAlt(): ?string
    {
        return $this->articleIntroImageAlt;
    }

    public function setArticleIntroImageAlt(string $articleIntroImageAlt): self
    {
        $this->articleIntroImageAlt = $articleIntroImageAlt;

        return $this;
    }

    public function getArticleIntroImageTitle(): ?string
    {
        return $this->articleIntroImageTitle;
    }

    public function setArticleIntroImageTitle(string $articleIntroImageTitle): self
    {
        $this->articleIntroImageTitle = $articleIntroImageTitle;

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
