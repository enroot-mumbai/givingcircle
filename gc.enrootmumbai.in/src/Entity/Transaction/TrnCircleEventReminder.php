<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventReminderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventReminderRepository::class)
 * @ORM\Table("trncircleeventreminder")
 */
class TrnCircleEventReminder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventReminders")
     */
    private $trnCircleEvents;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reminderDate;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $reminderSentByAppUser;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReminderDate(): ?\DateTimeInterface
    {
        return $this->reminderDate;
    }

    public function setReminderDate(?\DateTimeInterface $reminderDate): self
    {
        $this->reminderDate = $reminderDate;

        return $this;
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

    public function getReminderSentByAppUser(): ?AppUser
    {
        return $this->reminderSentByAppUser;
    }

    public function setReminderSentByAppUser(?AppUser $reminderSentByAppUser): self
    {
        $this->reminderSentByAppUser = $reminderSentByAppUser;

        return $this;
    }
}
