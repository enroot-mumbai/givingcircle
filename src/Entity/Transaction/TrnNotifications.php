<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstNotificationStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnNotificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnNotificationsRepository::class)
 * @ORM\Table("trnnotifications")
 */
class TrnNotifications
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
    private $appUser;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $shortName;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=MstNotificationStatus::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstNotificationStatus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $emailContent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $textMessage;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $whatsAppMessage;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $pushNotifications;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class)
     */
    private $trnCircle;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class)
     */
    private $trnCircleEvents;

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

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getMstNotificationStatus(): ?MstNotificationStatus
    {
        return $this->mstNotificationStatus;
    }

    public function setMstNotificationStatus(?MstNotificationStatus $mstNotificationStatus): self
    {
        $this->mstNotificationStatus = $mstNotificationStatus;

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

    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(?string $emailSubject): self
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    public function getEmailContent(): ?string
    {
        return $this->emailContent;
    }

    public function setEmailContent(?string $emailContent): self
    {
        $this->emailContent = $emailContent;

        return $this;
    }

    public function getTextMessage(): ?string
    {
        return $this->textMessage;
    }

    public function setTextMessage(?string $textMessage): self
    {
        $this->textMessage = $textMessage;

        return $this;
    }

    public function getWhatsAppMessage(): ?string
    {
        return $this->whatsAppMessage;
    }

    public function setWhatsAppMessage(?string $whatsAppMessage): self
    {
        $this->whatsAppMessage = $whatsAppMessage;

        return $this;
    }

    public function getPushNotifications(): ?string
    {
        return $this->pushNotifications;
    }

    public function setPushNotifications(?string $pushNotifications): self
    {
        $this->pushNotifications = $pushNotifications;

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

    public function getTrnCircleEvents(): ?TrnCircleEvents
    {
        return $this->trnCircleEvents;
    }

    public function setTrnCircleEvents(?TrnCircleEvents $trnCircleEvents): self
    {
        $this->trnCircleEvents = $trnCircleEvents;

        return $this;
    }
}
