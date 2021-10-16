<?php

namespace App\Entity\Master;

use App\Entity\Cms\CmsArticle;
use App\Repository\Master\MstAreaInterestRepository;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MstAreaInterestRepository::class)
 * @ORM\Table("mstareainterest")
 * @UniqueEntity(fields={"areaInterest"}, message="The value is already in the system")
 */
class MstAreaInterest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=120)
     */
    private $areaInterest;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $areaInterestDescription;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sequenceNo;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity=CmsArticle::class, mappedBy="mstAreaInterest")
     */
    private $cmsArticles;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     */
    private $mstAreaInterestPrimary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    public function __construct()
    {
        $this->cmsArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaInterest(): ?string
    {
        return $this->areaInterest;
    }

    public function setAreaInterest(string $areaInterest): self
    {
        $this->areaInterest = $areaInterest;

        return $this;
    }

    public function getAreaInterestDescription(): ?string
    {
        return $this->areaInterestDescription;
    }

    public function setAreaInterestDescription(?string $areaInterestDescription): self
    {
        $this->areaInterestDescription = $areaInterestDescription;

        return $this;
    }

    public function getSequenceNo(): ?int
    {
        return $this->sequenceNo;
    }

    public function setSequenceNo(int $sequenceNo): self
    {
        $this->sequenceNo = $sequenceNo;

        return $this;
    }

    public function getRowId(): ?string
    {
        return $this->rowId;
    }

    public function setRowId(string $rowId): self
    {
        $this->rowId = $rowId;

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

    public function __toString()
    {
        return $this->areaInterest;
    }

    /**
     * @return Collection|CmsArticle[]
     */
    public function getCmsArticles(): Collection
    {
        return $this->cmsArticles;
    }

    public function addCmsArticle(CmsArticle $cmsArticle): self
    {
        if (!$this->cmsArticles->contains($cmsArticle)) {
            $this->cmsArticles[] = $cmsArticle;
            $cmsArticle->addMstAreaInterest($this);
        }

        return $this;
    }

    public function removeCmsArticle(CmsArticle $cmsArticle): self
    {
        if ($this->cmsArticles->contains($cmsArticle)) {
            $this->cmsArticles->removeElement($cmsArticle);
            $cmsArticle->removeMstAreaInterest($this);
        }

        return $this;
    }

    public function getMstAreaInterestPrimary(): ?self
    {
        return $this->mstAreaInterestPrimary;
    }

    public function setMstAreaInterestPrimary(?self $mstAreaInterestPrimary): self
    {
        $this->mstAreaInterestPrimary = $mstAreaInterestPrimary;

        return $this;
    }

    public function getIcon(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
