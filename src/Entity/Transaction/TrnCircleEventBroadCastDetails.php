<?php

namespace App\Entity\Transaction;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnCircleEventBroadCastDetailsRepository;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCircleEventBroadCastDetailsRepository::class)
 * @ORM\Table("trncircleeventbroadcastdetails")
 */
class TrnCircleEventBroadCastDetails
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
     * @ORM\Column(type="date")
     */
    private $boardCastDate;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trnCircleEventBroadCastDetails")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trnCircleEvent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleUploadDocuments::class, mappedBy="trnCircleEventBroadCast")
     */
    private $trnCircleUploadDocuments;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircle::class, inversedBy="trnCircleEventBroadCastDetails")
     */
    private $trnCircle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sentTo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\ManyToMany(targetEntity=AppUser::class)
     */
    private $sentToAppUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadedFile;

    public function __construct()
    {
        $this->trnCircleUploadDocuments = new ArrayCollection();
        $this->sentToAppUser = new ArrayCollection();
    }

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

    public function getBoardCastDate(): ?\DateTimeInterface
    {
        return $this->boardCastDate;
    }

    public function setBoardCastDate(\DateTimeInterface $boardCastDate): self
    {
        $this->boardCastDate = $boardCastDate;

        return $this;
    }

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

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

    /**
     * @return Collection|TrnCircleUploadDocuments[]
     */
    public function getTrnCircleUploadDocuments(): Collection
    {
        return $this->trnCircleUploadDocuments;
    }

    public function addTrnCircleUploadDocument(TrnCircleUploadDocuments $trnCircleUploadDocument): self
    {
        if (!$this->trnCircleUploadDocuments->contains($trnCircleUploadDocument)) {
            $this->trnCircleUploadDocuments[] = $trnCircleUploadDocument;
            $trnCircleUploadDocument->setTrnCircleEventBroadCast($this);
        }

        return $this;
    }

    public function removeTrnCircleUploadDocument(TrnCircleUploadDocuments $trnCircleUploadDocument): self
    {
        if ($this->trnCircleUploadDocuments->contains($trnCircleUploadDocument)) {
            $this->trnCircleUploadDocuments->removeElement($trnCircleUploadDocument);
            // set the owning side to null (unless already changed)
            if ($trnCircleUploadDocument->getTrnCircleEventBroadCast() === $this) {
                $trnCircleUploadDocument->setTrnCircleEventBroadCast(null);
            }
        }

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

    public function getTrnCircle(): ?TrnCircle
    {
        return $this->trnCircle;
    }

    public function setTrnCircle(?TrnCircle $trnCircle): self
    {
        $this->trnCircle = $trnCircle;

        return $this;
    }

    public function getSentTo(): ?string
    {
        return $this->sentTo;
    }

    public function setSentTo(?string $sentTo): self
    {
        $this->sentTo = $sentTo;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection|AppUser[]
     */
    public function getSentToAppUser(): Collection
    {
        return $this->sentToAppUser;
    }

    public function addSentToAppUser(AppUser $sentToAppUser): self
    {
        if (!$this->sentToAppUser->contains($sentToAppUser)) {
            $this->sentToAppUser[] = $sentToAppUser;
        }

        return $this;
    }

    public function removeSentToAppUser(AppUser $sentToAppUser): self
    {
        $this->sentToAppUser->removeElement($sentToAppUser);

        return $this;
    }

    public function getUploadedFile(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'. $this->uploadedFile;
    }

    public function setUploadedFile(?string $uploadedFile): self
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }
}
