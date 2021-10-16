<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstProductCategory;
use App\Entity\Master\MstProductSubCategory;
use App\Entity\Organization\OrgCompany;
use App\Entity\Seo\SeoContent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsPageRepository")
 * @ORM\Table("cmspage")
 */
class CmsPage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pageName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pageTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slugName;

    /**
     * @ORM\Column(type="integer")
     */
    private $parentId;

    /**
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $pageRoute;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=CmsPageContent::class, cascade={"persist"}, mappedBy="cmsPage")
     */
    private $cmsPageContent;

    /**
     * @ORM\OneToOne(targetEntity=SeoContent::class, mappedBy="cmsPage", cascade={"persist", "remove"})
     */
    private $seoContent;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cmsIntroEmail;

    /**
     * @ORM\Column(type="integer")
     */
    private $displaySequence;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $bannerDesktopImageSize;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $bannerTabletImageSize;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $bannerMobileImageSize;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdateTime;

    public function __construct()
    {
        $this->cmsPageContent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    public function setPageName(string $pageName): self
    {
        $this->pageName = $pageName;

        return $this;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(string $pageTitle): self
    {
        $this->pageTitle = $pageTitle;

        return $this;
    }

    public function getSlugName(): ?string
    {
        return $this->slugName;
    }

    public function setSlugName(string $slugName): self
    {
        $this->slugName = $slugName;
        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getPageRoute(): ?string
    {
        return $this->pageRoute;
    }

    public function setPageRoute(?string $pageRoute): self
    {
        $this->pageRoute = $pageRoute;
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
        return $this->pageName;
    }

    /**
     * @return Collection|CmsPageContent[]
     */
    public function getCmsPageContent(): Collection
    {
        return $this->cmsPageContent;
    }

    public function addCmsPageContent(CmsPageContent $cmsPageContent): self
    {
        if (!$this->cmsPageContent->contains($cmsPageContent)) {
            $this->cmsPageContent[] = $cmsPageContent;
            $cmsPageContent->setCmsPage($this);
        }

        return $this;
    }

    public function removeCmsPageContent(CmsPageContent $cmsPageContent): self
    {
        if ($this->cmsPageContent->contains($cmsPageContent)) {
            $this->cmsPageContent->removeElement($cmsPageContent);
            // set the owning side to null (unless already changed)
            if ($cmsPageContent->getCmsPage() === $this) {
                $cmsPageContent->setCmsPage(null);
            }
        }

        return $this;
    }

    public function getSeoContent(): ?SeoContent
    {
        return $this->seoContent;
    }

    public function setSeoContent(?SeoContent $seoContent): self
    {
        // unset the owning side of the relation if necessary
        if ($seoContent === null && $this->seoContent !== null) {
            $this->seoContent->setCmsPage(null);
        }

        // set the owning side of the relation if necessary
        if ($seoContent !== null && $seoContent->getCmsPage() !== $this) {
            $seoContent->setCmsPage($this);
        }

        $this->seoContent = $seoContent;

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

    public function getCmsIntroEmail(): ?string
    {
        return $this->cmsIntroEmail;
    }

    public function setCmsIntroEmail(string $cmsIntroEmail): self
    {
        $this->cmsIntroEmail = $cmsIntroEmail;

        return $this;
    }

    public function getDisplaySequence(): ?int
    {
        return $this->displaySequence;
    }

    public function setDisplaySequence(int $displaySequence): self
    {
        $this->displaySequence = $displaySequence;

        return $this;
    }

    public function getBannerDesktopImageSize(): ?string
    {
        return $this->bannerDesktopImageSize;
    }

    public function setBannerDesktopImageSize(string $bannerDesktopImageSize): self
    {
        $this->bannerDesktopImageSize = $bannerDesktopImageSize;

        return $this;
    }

    public function getBannerTabletImageSize(): ?string
    {
        return $this->bannerTabletImageSize;
    }

    public function setBannerTableImageSize(string $bannerTabletImageSize): self
    {
        $this->bannerTabletImageSize = $bannerTabletImageSize;

        return $this;
    }

    public function getBannerMobileImageSize(): ?string
    {
        return $this->bannerMobileImageSize;
    }

    public function setBannerMobileImageSize(string $bannerMobileImageSize): self
    {
        $this->bannerMobileImageSize = $bannerMobileImageSize;

        return $this;
    }

    public function getLastUpdateTime(): ?\DateTime
    {
        return $this->lastUpdateTime;
    }

    public function setLastUpdateTime(?\DateTime $lastUpdateTime): self
    {
        $this->lastUpdateTime = $lastUpdateTime;

        return $this;
    }

}
