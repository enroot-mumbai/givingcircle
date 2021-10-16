<?php

namespace App\Entity\Cms;

use App\Entity\SystemApp\AppUser;
use App\Repository\Cms\CmsNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CmsNotificationRepository::class)
 * @ORM\Table("cmsnotification")
 * @UniqueEntity(fields={"notificationName"}, message="The value is already in the system")
 */
class CmsNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank
     */
    private $notificationName;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $emailSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $whatappMsg;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $textMessage;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $pushNotification;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $createdByAppUser;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $systemNotification;

    /**
     * @ORM\Column(type="guid", nullable=true)
     */
    private $rowId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotificationName(): ?string
    {
        return $this->notificationName;
    }

    public function setNotificationName(?string $notificationName): self
    {
        $this->notificationName = $notificationName;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWhatappMsg(): ?string
    {
        return $this->whatappMsg;
    }

    public function setWhatappMsg(?string $whatappMsg): self
    {
        $this->whatappMsg = $whatappMsg;

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

    public function getPushNotification(): ?string
    {
        return $this->pushNotification;
    }

    public function setPushNotification(?string $pushNotification): self
    {
        $this->pushNotification = $pushNotification;

        return $this;
    }

    public function getCreatedByAppUser(): ?AppUser
    {
        return $this->createdByAppUser;
    }

    public function setCreatedByAppUser(?AppUser $createdByAppUser): self
    {
        $this->createdByAppUser = $createdByAppUser;

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

    public function getSystemNotification(): ?string
    {
        return $this->systemNotification;
    }

    public function setSystemNotification(?string $systemNotification): self
    {
        $this->systemNotification = $systemNotification;

        return $this;
    }

    public function getRowId(): ?string
    {
        return $this->rowId;
    }

    public function setRowId(?string $rowId): self
    {
        $this->rowId = $rowId;

        return $this;
    }
}
