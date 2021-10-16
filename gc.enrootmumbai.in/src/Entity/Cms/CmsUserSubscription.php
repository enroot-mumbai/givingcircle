<?php

namespace App\Entity\Cms;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsUserSubscriptionRepository")
 * @ORM\Table("cmsusersubscription")
 */
class CmsUserSubscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $userSubscriptionEmail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSubscriptionActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $subscriptionDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $subscriptionOptOutDateTime;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppUser(): ?AppUser
    {
        return $this->appUser;
    }

    public function setAppUser(?AppUser $appUser): self
    {
        $this->appUser = $appUser;

        return $this;
    }

    public function getUserSubscriptionEmail(): ?string
    {
        return $this->userSubscriptionEmail;
    }

    public function setUserSubscriptionEmail(string $userSubscriptionEmail): self
    {
        $this->userSubscriptionEmail = $userSubscriptionEmail;

        return $this;
    }

    public function getIsSubscriptionActive(): ?bool
    {
        return $this->isSubscriptionActive;
    }

    public function setIsSubscriptionActive(bool $isSubscriptionActive): self
    {
        $this->isSubscriptionActive = $isSubscriptionActive;

        return $this;
    }

    public function getSubscriptionDateTime(): ?DateTimeInterface
    {
        return $this->subscriptionDateTime;
    }

    public function setSubscriptionDateTime(?DateTimeInterface $subscriptionDateTime): self
    {
        $this->subscriptionDateTime = $subscriptionDateTime;

        return $this;
    }

    public function getSubscriptionOptOutDateTime(): ?DateTimeInterface
    {
        return $this->subscriptionOptOutDateTime;
    }

    public function setSubscriptionOptOutDateTime(?DateTimeInterface $subscriptionOptOutDateTime): self
    {
        $this->subscriptionOptOutDateTime = $subscriptionOptOutDateTime;

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
