<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstRating;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventCommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventCommentsRepository::class)
 * @ORM\Table("trncircleeventcomments")
 */
class TrnCircleEventComments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleEventComments")
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEventComments::class, inversedBy="trnCircleEventComments")
     */
    private $parentComment;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleEventComments::class, mappedBy="parentComment")
     */
    private $trnCircleEventComments;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $commentDateTime;

    /**
     * @ORM\ManyToOne(targetEntity=MstRating::class)
     */
    private $mstRating;

    /**
     * @ORM\Column(type="boolean", nullable=true)
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
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventComments")
     */
    private $trnCircleEvents;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likeCount;

    /**
     * @ORM\ManyToOne (targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    public function __construct()
    {
        $this->trnCircleEventComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|TrnCircleEventComments[]
     */
    public function getTrnCircleEventComments(): Collection
    {
        return $this->trnCircleEventComments;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCommentorName(): ?string
    {
        return $this->commentorName;
    }

    public function setCommentorName(?string $commentorName): self
    {
        $this->commentorName = $commentorName;

        return $this;
    }

    public function getCommentorEmail(): ?string
    {
        return $this->commentorEmail;
    }

    public function setCommentorEmail(?string $commentorEmail): self
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

    public function getCommentDateTime(): ?\DateTimeInterface
    {
        return $this->commentDateTime;
    }

    public function setCommentDateTime(?\DateTimeInterface $commentDateTime): self
    {
        $this->commentDateTime = $commentDateTime;

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

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(?bool $isApproved): self
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

    public function getTrnCircleEvents(): ?TrnCircleEvents
    {
        return $this->trnCircleEvents;
    }

    public function setTrnCircleEvents(?TrnCircleEvents $trnCircleEvents): self
    {
        $this->trnCircleEvents = $trnCircleEvents;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }

    public function setLikeCount(?int $likeCount): self
    {
        $this->likeCount = $likeCount;

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

    public function __toString()
    {
        return (string) $this->getComment();
    }
}