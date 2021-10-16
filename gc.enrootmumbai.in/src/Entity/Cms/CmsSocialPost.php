<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstSocial;
use App\Repository\Cms\CmsSocialPostRepository;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsSocialPostRepository::class)
 * @ORM\Table("cmssocialpost")
 */
class CmsSocialPost
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
    private $postLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postCaption;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postMessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postPicture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postPicturePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postPictureUrl;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPublish;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $postStatus;

    /**
     * @ORM\ManyToMany(targetEntity=MstSocial::class)
     */
    private $mstSocial;

    /**
     * @ORM\OneToMany(targetEntity=CmsSocial::class, cascade={"persist"}, mappedBy="cmsSocialPost")
     */
    private $cmsSocial;

    public function __construct()
    {
        $this->mstSocial = new ArrayCollection();
        $this->cmsSocial = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostLink(): ?string
    {
        return $this->postLink;
    }

    public function setPostLink(?string $postLink): self
    {
        $this->postLink = $postLink;

        return $this;
    }

    public function getPostDescription(): ?string
    {
        return $this->postDescription;
    }

    public function setPostDescription(?string $postDescription): self
    {
        $this->postDescription = $postDescription;

        return $this;
    }

    public function getPostCaption(): ?string
    {
        return $this->postCaption;
    }

    public function setPostCaption(?string $postCaption): self
    {
        $this->postCaption = $postCaption;

        return $this;
    }

    public function getPostMessage(): ?string
    {
        return $this->postMessage;
    }

    public function setPostMessage(?string $postMessage): self
    {
        $this->postMessage = $postMessage;

        return $this;
    }

    public function getPostPicture(): ?string
    {
        return $this->postPicture;
    }

    public function setPostPicture(?string $postPicture): self
    {
        $this->postPicture = $postPicture;

        return $this;
    }

    public function getPostPicturePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getPostPicture();
    }

    public function setPostPicturePath(?string $postPicturePath): self
    {
        $this->postPicturePath = $postPicturePath;

        return $this;
    }

    public function getPostPictureUrl(): ?string
    {
        return $this->postPictureUrl;
    }

    public function setPostPictureUrl(?string $postPictureUrl): self
    {
        $this->postPictureUrl = $postPictureUrl;

        return $this;
    }

    public function getIsPublish(): ?bool
    {
        return $this->isPublish;
    }

    public function setIsPublish(bool $isPublish): self
    {
        $this->isPublish = $isPublish;
        return $this;
    }

    public function getPostStatus(): ?string
    {
        return $this->postStatus;
    }

    public function setPostStatus(string $postStatus): self
    {
        $this->postStatus = $postStatus;

        return $this;
    }

    /**
     * @return Collection|MstSocial[]
     */
    public function getMstSocial(): Collection
    {
        return $this->mstSocial;
    }

    public function addMstSocial(MstSocial $mstSocial): self
    {
        if (!$this->mstSocial->contains($mstSocial)) {
            $this->mstSocial[] = $mstSocial;
        }

        return $this;
    }

    public function removeMstSocial(MstSocial $mstSocial): self
    {
        if ($this->mstSocial->contains($mstSocial)) {
            $this->mstSocial->removeElement($mstSocial);
        }

        return $this;
    }

    /**
     * @return Collection|CmsSocial[]
     */
    public function getCmsSocial(): Collection
    {
        return $this->cmsSocial;
    }

    public function addCmsSocial(CmsSocial $cmsSocial): self
    {
        if (!$this->cmsSocial->contains($cmsSocial)) {
            $this->cmsSocial[] = $cmsSocial;
            $cmsSocial->setCmsSocialPost($this);
        }

        return $this;
    }

    public function removeCmsSocial(CmsSocial $cmsSocial): self
    {
        if ($this->cmsSocial->contains($cmsSocial)) {
            $this->cmsSocial->removeElement($cmsSocial);
            // set the owning side to null (unless already changed)
            if ($cmsSocial->getCmsSocialPost() === $this) {
                $cmsSocial->setCmsSocialPost(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->postLink;
    }

}
