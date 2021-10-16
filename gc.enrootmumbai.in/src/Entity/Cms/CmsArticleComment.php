<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstRating;
use App\Entity\SystemApp\AppUser;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsArticleCommentRepository")
 * @ORM\Table("cmsarticlecomment")
 */
class CmsArticleComment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CmsArticle::class, inversedBy="cmsArticleComment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmsArticle;

    /**
     * @ORM\ManyToOne(targetEntity=CmsArticleComment::class, inversedBy="cmsArticleComment")
     */
    private $parentComment;

    /**
     * @ORM\OneToMany(targetEntity=CmsArticleComment::class, mappedBy="parentComment")
     */
    private $cmsArticleComment;

    /**
     * @ORM\Column(type="text")
     */
    private $articleComment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commentorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $commentorEmail;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $commentorWebsite;

    /**
     * @ORM\Column(type="datetime")
     */
    private $commentDateTime;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $commentLikeCount;

    /**
     * @ORM\ManyToOne(targetEntity=MstRating::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstRating;

    /**
     * @ORM\ManyToOne (targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isApproved;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    public function __construct()
    {
        $this->cmsArticleComment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): self
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCmsArticleComment(): Collection
    {
        return $this->cmsArticleComment;
    }

    public function addCmsArticleComment(self $cmsArticleComment): self
    {
        if (!$this->cmsArticleComment->contains($cmsArticleComment)) {
            $this->cmsArticleComment[] = $cmsArticleComment;
            $cmsArticleComment->setParentComment($this);
        }

        return $this;
    }

    public function removeCmsArticleComment(self $cmsArticleComment): self
    {
        if ($this->cmsArticleComment->contains($cmsArticleComment)) {
            $this->cmsArticleComment->removeElement($cmsArticleComment);
            // set the owning side to null (unless already changed)
            if ($cmsArticleComment->getParentComment() === $this) {
                $cmsArticleComment->setParentComment(null);
            }
        }

        return $this;
    }

    public function getArticleComment(): ?string
    {
        return $this->articleComment;
    }

    public function setArticleComment(string $articleComment): self
    {
        $this->articleComment = $articleComment;

        return $this;
    }

    public function getCommentorName(): ?string
    {
        return $this->commentorName;
    }

    public function setCommentorName(string $commentorName): self
    {
        $this->commentorName = $commentorName;

        return $this;
    }

    public function getCommentorEmail(): ?string
    {
        return $this->commentorEmail;
    }

    public function setCommentorEmail(string $commentorEmail): self
    {
        $this->commentorEmail = $commentorEmail;

        return $this;
    }

    public function getCommentorWebsite(): ?string
    {
        return $this->commentorWebsite;
    }

    public function setCommentorWebsite(?string $commentorWebsite): self
    {
        $this->commentorWebsite = $commentorWebsite;

        return $this;
    }

    public function getCommentDateTime(): ?DateTimeInterface
    {
        return $this->commentDateTime;
    }

    public function setCommentDateTime(DateTimeInterface $CommentDateTime): self
    {
        $this->commentDateTime = $CommentDateTime;

        return $this;
    }

    public function getCommentLikeCount(): ?int
    {
        return $this->commentLikeCount;
    }

    public function setCommentLikeCount(?int $commentLikeCount): self
    {
        $this->commentLikeCount = $commentLikeCount;
        return $this;
    }

    public function getMstRating(): ?MstRating
    {
        return $this->mstRating;
    }

    public function setMstRating(?MstRating $mstRating): self
    {
        $this->mstRating = $mstRating;
        return $this;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(AppUser $appUser): self
    {
        $this->appUser = $appUser;
        return $this;
    }

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

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
