<?php

namespace App\Entity\Transaction;

use App\Repository\Transaction\TrnCrowdFundEventDocumentsRepository;
use App\Service\FileUploaderHelper;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnCrowdFundEventDocumentsRepository::class)
 * @ORM\Table("trncrowdfundeventdocuments")
 */
class TrnCrowdFundEventDocuments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCrowdFundEvent::class, inversedBy="trnCrowdFundEventDocuments")
     */
    private $trnCrowdFundEvent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadedFilePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $documentCaption;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrnCrowdFundEvent(): ?TrnCrowdFundEvent
    {
        return $this->trnCrowdFundEvent;
    }

    public function setTrnCrowdFundEvent(?TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        $this->trnCrowdFundEvent = $trnCrowdFundEvent;

        return $this;
    }

    public function getUploadedFilePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'. $this->uploadedFilePath;
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
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = null;
    }
}
