<?php

namespace App\Entity\Cms;

use App\Repository\Cms\CmsArticleContentRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CmsArticleContentRepository::class)
 * @ORM\Table("cmsarticlecontent")
 * @UniqueEntity(fields={"articleContentImageSetName"}, message="The value is already in the system")
 */
class CmsArticleContent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $articleTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $articleContent;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $mediaType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentDesktopImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentTabletImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentMobileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentImageSetName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentImageAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentImageTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleContentDesktopImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleContentTabletImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleContentMobileImagePath;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $articleContentVideo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleContentVideoPath;

    /**
     * @ORM\ManyToOne(targetEntity=CmsArticle::class, inversedBy="cmsArticleContent")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cmsArticle;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleContent(): ?string
    {
        return $this->articleContent;
    }

    public function setArticleContent(?string $articleContent): self
    {
        $this->articleContent = $articleContent;

        return $this;
    }

    public function getArticleTitle(): ?string
    {
        return $this->articleTitle;
    }

    public function setArticleTitle(?string $articleTitle): self
    {
        $this->articleTitle = $articleTitle;

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

    public function getArticleContentDesktopImage(): ?string
    {
        return $this->articleContentDesktopImage;
    }

    public function setArticleContentDesktopImage(?string $articleContentDesktopImage): self
    {
        $this->articleContentDesktopImage = $articleContentDesktopImage;

        return $this;
    }

    public function getArticleContentTabletImage(): ?string
    {
        return $this->articleContentTabletImage;
    }

    public function setArticleContentTabletImage(?string $articleContentTabletImage): self
    {
        $this->articleContentTabletImage = $articleContentTabletImage;

        return $this;
    }

    public function getArticleContentMobileImage(): ?string
    {
        return $this->articleContentMobileImage;
    }

    public function setArticleContentMobileImage(?string $articleContentMobileImage): self
    {
        $this->articleContentMobileImage = $articleContentMobileImage;

        return $this;
    }

    public function getArticleContentImageSetName(): ?string
    {
        return $this->articleContentImageSetName;
    }

    public function setArticleContentImageSetName(?string $articleContentImageSetName): self
    {
        $this->articleContentImageSetName = $articleContentImageSetName;

        return $this;
    }

    public function getArticleContentImageAlt(): ?string
    {
        return $this->articleContentImageAlt;
    }

    public function setArticleContentImageAlt(?string $articleContentImageAlt): self
    {
        $this->articleContentImageAlt = $articleContentImageAlt;

        return $this;
    }

    public function getArticleContentImageTitle(): ?string
    {
        return $this->articleContentImageTitle;
    }

    public function setArticleContentImageTitle(?string $articleContentImageTitle): self
    {
        $this->articleContentImageTitle = $articleContentImageTitle;

        return $this;
    }

    public function getArticleContentDesktopImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleContentDesktopImage();
    }

    public function setArticleContentDesktopImagePath(?string $articleContentDesktopImagePath): self
    {
        $this->articleContentDesktopImagePath = $articleContentDesktopImagePath;

        return $this;
    }

    public function getArticleContentTabletImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleContentTabletImage();
    }

    public function setArticleContentTabletImagePath(?string $articleContentTabletImagePath): self
    {
        $this->articleContentTabletImagePath = $articleContentTabletImagePath;

        return $this;
    }

    public function getArticleContentMobileImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleContentMobileImage();
    }

    public function setArticleContentMobileImagePath(?string $articleContentMobileImagePath): self
    {
        $this->articleContentMobileImagePath = $articleContentMobileImagePath;

        return $this;
    }

    public function getArticleContentVideo(): ?string
    {
        return $this->articleContentVideo;
    }

    public function setArticleContentVideo(?string $articleContentVideo): self
    {
        $this->articleContentVideo = $articleContentVideo;
        return $this;
    }

    public function getArticleContentVideoPath(): ?string
    {
        return $this->articleContentVideoPath;
    }

    public function setArticleContentVideoPath(?string $articleContentVideoPath): self
    {
        $this->articleContentVideoPath = $articleContentVideoPath;
        return $this;
    }


    public function __toString()
    {
        if (null === $this->articleContent )
        {
            return "";
        }
        return $this->articleContent;
    }

    public function getCmsArticle(): ?CmsArticle
    {
        return $this->cmsArticle;
    }

    public function setCmsArticle(?CmsArticle $cmsArticle): self
    {
        $this->cmsArticle = $cmsArticle;

        return $this;
    }

}
