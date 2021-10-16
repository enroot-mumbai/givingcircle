<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunteerDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunteerDocumentRepository::class)
 * @ORM\Table("trnvolunteerdocument")
 */
class TrnVolunteerDocument
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadedFilePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $documentCaption;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterDetail::class, inversedBy="trnVolunteerDocuments")
     */
    private $trnVolunterDetail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $uploadedOn;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $appUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadedFilePath(): ?string
    {
        return $this->uploadedFilePath;
    }

    public function setUploadedFilePath(?string $uploadedFilePath): self
    {
        $this->uploadedFilePath = $uploadedFilePath;

        return $this;
    }

    public function getDocumentCaption(): ?string
    {
        return $this->documentCaption;
    }

    public function setDocumentCaption(?string $documentCaption): self
    {
        $this->documentCaption = $documentCaption;

        return $this;
    }

    public function getTrnVolunterDetail(): ?TrnVolunterDetail
    {
        return $this->trnVolunterDetail;
    }

    public function setTrnVolunterDetail(?TrnVolunterDetail $trnVolunterDetail): self
    {
        $this->trnVolunterDetail = $trnVolunterDetail;

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

    public function getUploadedOn(): ?\DateTimeInterface
    {
        return $this->uploadedOn;
    }

    public function setUploadedOn(?\DateTimeInterface $uploadedOn): self
    {
        $this->uploadedOn = $uploadedOn;

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
}
