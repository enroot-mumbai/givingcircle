<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCommentsOnUserPastWorkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCommentsOnUserPastWorkRepository::class)
 * @ORM\Table("trncommentsonuserpastwork")
 */
class TrnCommentsOnUserPastWork
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUserCommentedBy;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $userReaction;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppUserCommentedBy(): ?AppUser
    {
        return $this->appUserCommentedBy;
    }

    public function setAppUserCommentedBy(?AppUser $appUserCommentedBy): self
    {
        $this->appUserCommentedBy = $appUserCommentedBy;

        return $this;
    }

    public function getUserReaction(): ?string
    {
        return $this->userReaction;
    }

    public function setUserReaction(string $userReaction): self
    {
        $this->userReaction = $userReaction;

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

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

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

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(string $comments): self
    {
        $this->comments = $comments;

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
