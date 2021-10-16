<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstSocial;
use App\Repository\Cms\CmsSocialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsSocialRepository::class)
 * @ORM\Table("cmssocial")
 */
class CmsSocial
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
    private $socialSharePhotoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $socialSharePostId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $socialApiLog = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSocialPostDeleted;

    /**
     * @ORM\ManyToOne(targetEntity=MstSocial::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstSocial;

    /**
     * @ORM\ManyToOne(targetEntity=CmsSocialPost::class, inversedBy="cmsSocial")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmsSocialPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSocialSharePhotoId(): ?string
    {
        return $this->socialSharePhotoId;
    }

    public function setSocialSharePhotoId(?string $socialSharePhotoId): self
    {
        $this->socialSharePhotoId = $socialSharePhotoId;

        return $this;
    }

    public function getSocialSharePostId(): ?string
    {
        return $this->socialSharePostId;
    }

    public function setSocialSharePostId(?string $socialSharePostId): self
    {
        $this->socialSharePostId = $socialSharePostId;

        return $this;
    }

    public function getSocialApiLog(): ?array
    {
        return $this->socialApiLog;
    }

    public function setSocialApiLog(?array $socialApiLog): self
    {
        $this->socialApiLog = $socialApiLog;

        return $this;
    }

    public function getIsSocialPostDeleted(): ?bool
    {
        return $this->isSocialPostDeleted;
    }

    public function setIsSocialPostDeleted(?bool $isSocialPostDeleted): self
    {
        $this->isSocialPostDeleted = $isSocialPostDeleted;

        return $this;
    }

    public function getMstSocial(): ?MstSocial
    {
        return $this->mstSocial;
    }

    public function setMstSocial(?MstSocial $mstSocial): self
    {
        $this->mstSocial = $mstSocial;

        return $this;
    }

    public function getCmsSocialPost(): ?CmsSocialPost
    {
        return $this->cmsSocialPost;
    }

    public function setCmsSocialPost(?CmsSocialPost $cmsSocialPost): self
    {
        $this->cmsSocialPost = $cmsSocialPost;

        return $this;
    }
}
