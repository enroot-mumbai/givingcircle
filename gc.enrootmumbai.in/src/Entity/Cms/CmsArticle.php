<?php

namespace App\Entity\Cms;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstArticleCategory;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Service\FileUploaderHelper;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsArticleRepository")
 * @ORM\Table("cmsarticle")
 * @UniqueEntity(fields={"articleIntroImageSetName"}, message="The value is already in the system")
 */
class CmsArticle
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
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $articleCreator;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $articleCreatorUrl;

    /**
     * @ORM\ManyToOne(targetEntity=MstArticleCategory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstArticleCategory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleFor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $articleSlugName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleCanonicalUrl;

    /**
     * @ORM\Column(type="text")
     */
    private $articleIntro;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $introMediaType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroDesktopImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroTabletImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroMobileImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageSetName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageAlt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $articleIntroImageTitle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleIntroDesktopImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleIntroTabletImagePath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $articleIntroMobileImagePath;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $articleIntroVideo;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $articleIntroVideoPath;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $articleViewCount;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $articleLikeCount;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $articleShareCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ogTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ogDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ogType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ogImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ogImagePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaKeyword;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $focusKeyPhrase;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $keyPhraseSynonyms;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $seoSchema;

    /**
     * @ORM\Column(type="datetime")
     */
    private $articleCreateDateTime;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $articleCreatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $articleUpdateDateTime;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $articleUpdatedBy;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cityName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $locationName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstCountry")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCountry;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstState")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Master\MstCity")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="smallint", nullable=true)
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
     * @ORM\OneToMany(targetEntity=CmsArticleComment::class, mappedBy="cmsArticle")
     */
    private $cmsArticleComment;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    /**
     * @ORM\OneToMany(targetEntity=CmsArticleContent::class, mappedBy="cmsArticle", cascade={"persist","remove"})
     */
    private $cmsArticleContent;

    /**
     * @ORM\ManyToMany(targetEntity=MstAreaInterest::class, inversedBy="cmsArticles")
     */
    private $mstAreaInterest;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     */
    private $changeMakerAppUser;

    public function __construct()
    {
        $this->cmsArticleComment = new ArrayCollection();
        $this->cmsArticleContent = new ArrayCollection();
        $this->mstAreaInterest = new ArrayCollection();
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

    public function getArticleCreator(): ?string
    {
        return $this->articleCreator;
    }

    public function setArticleCreator(string $articleCreator): self
    {
        $this->articleCreator = $articleCreator;
        return $this;
    }

    public function getArticleCreatorUrl(): ?string
    {
        return $this->articleCreatorUrl;
    }

    public function setArticleCreatorUrl(string $articleCreatorUrl): self
    {
        $this->articleCreatorUrl = $articleCreatorUrl;
        return $this;
    }

    public function getMstArticleCategory(): ?MstArticleCategory
    {
        return $this->mstArticleCategory;
    }

    public function setMstArticleCategory(?MstArticleCategory $mstArticleCategory): self
    {
        $this->mstArticleCategory = $mstArticleCategory;
        return $this;
    }

    public function getArticleFor(): ?string
    {
        return $this->articleFor;
    }

    public function setArticleFor(string $articleFor): self
    {
        $this->articleFor = $articleFor;

        return $this;
    }

    public function getArticleTitle(): ?string
    {
        return $this->articleTitle;
    }

    public function setArticleTitle(string $articleTitle): self
    {
        $this->articleTitle = $articleTitle;
        return $this;
    }

    public function getArticleSlugName(): ?string
    {
        return $this->articleSlugName;
    }

    public function setArticleSlugName(string $articleSlugName): self
    {
        $this->articleSlugName = $articleSlugName;
        return $this;
    }

    public function getArticleCanonicalUrl(): ?string
    {
        return $this->articleCanonicalUrl;
    }

    public function setArticleCanonicalUrl(string $articleCanonicalUrl): self
    {
        $this->articleCanonicalUrl = $articleCanonicalUrl;
        return $this;
    }

    public function getArticleIntro(): ?string
    {
        return $this->articleIntro;
    }

    public function setArticleIntro(?string $articleIntro): self
    {
        $this->articleIntro = $articleIntro;
        return $this;
    }

    public function getIntroMediaType(): ?string
    {
        return $this->introMediaType;
    }

    public function setIntroMediaType(string $introMediaType): self
    {
        $this->introMediaType = $introMediaType;

        return $this;
    }

    public function getArticleIntroDesktopImage(): ?string
    {
        return $this->articleIntroDesktopImage;
    }

    public function setArticleIntroDesktopImage(?string $articleIntroDesktopImage): self
    {
        $this->articleIntroDesktopImage = $articleIntroDesktopImage;

        return $this;
    }

    public function getArticleIntroTabletImage(): ?string
    {
        return $this->articleIntroTabletImage;
    }

    public function setArticleIntroTabletImage(?string $articleIntroTabletImage): self
    {
        $this->articleIntroTabletImage = $articleIntroTabletImage;

        return $this;
    }

    public function getArticleIntroMobileImage(): ?string
    {
        return $this->articleIntroMobileImage;
    }

    public function setArticleIntroMobileImage(?string $articleIntroMobileImage): self
    {
        $this->articleIntroMobileImage = $articleIntroMobileImage;

        return $this;
    }

    public function getArticleIntroImageSetName(): ?string
    {
        return $this->articleIntroImageSetName;
    }

    public function setArticleIntroImageSetName(string $articleIntroImageSetName): self
    {
        $this->articleIntroImageSetName = $articleIntroImageSetName;

        return $this;
    }

    public function getArticleIntroImageAlt(): ?string
    {
        return $this->articleIntroImageAlt;
    }

    public function setArticleIntroImageAlt(string $articleIntroImageAlt): self
    {
        $this->articleIntroImageAlt = $articleIntroImageAlt;

        return $this;
    }

    public function getArticleIntroImageTitle(): ?string
    {
        return $this->articleIntroImageTitle;
    }

    public function setArticleIntroImageTitle(string $articleIntroImageTitle): self
    {
        $this->articleIntroImageTitle = $articleIntroImageTitle;

        return $this;
    }

    public function getArticleIntroDesktopImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleIntroDesktopImage();
    }

    public function setArticleIntroDesktopImagePath(string $articleIntroDesktopImagePath): self
    {
        $this->articleIntroDesktopImagePath = $articleIntroDesktopImagePath;

        return $this;
    }

    public function getArticleIntroTabletImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleIntroTabletImage();
    }

    public function setArticleIntroTabletImagePath(?string $articleIntroTabletImagePath): self
    {
        $this->articleIntroTabletImagePath = $articleIntroTabletImagePath;

        return $this;
    }

    public function getArticleIntroMobileImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getArticleIntroMobileImage();
    }

    public function setArticleIntroMobileImagePath(?string $articleIntroMobileImagePath): self
    {
        $this->articleIntroMobileImagePath = $articleIntroMobileImagePath;

        return $this;
    }

    public function getArticleIntroVideo(): ?string
    {
        return $this->articleIntroVideo;
    }

    public function setArticleIntroVideo(?string $articleIntroVideo): self
    {
        $this->articleIntroVideo = $articleIntroVideo;
        return $this;
    }

    public function getArticleIntroVideoPath(): ?string
    {
        return $this->articleIntroVideoPath;
    }

    public function setArticleIntroVideoPath(?string $articleIntroVideoPath): self
    {
        $this->articleIntroVideoPath = $articleIntroVideoPath;
        return $this;
    }

    public function getArticleLikeCount(): ?int
    {
        return $this->articleLikeCount;
    }

    public function setArticleLikeCount(?int $articleLikeCount): self
    {
        $this->articleLikeCount = $articleLikeCount;
        return $this;
    }

    public function getArticleShareCount(): ?int
    {
        return $this->articleShareCount;
    }

    public function setArticleShareCount(?int $articleShareCount): self
    {
        $this->articleShareCount = $articleShareCount;
        return $this;
    }

    public function getArticleViewCount(): ?int
    {
        return $this->articleViewCount;
    }

    public function setArticleViewCount(?int $articleViewCount): self
    {
        $this->articleViewCount = $articleViewCount;
        return $this;
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function setOgTitle(?string $ogTitle): self
    {
        $this->ogTitle = $ogTitle;
        return $this;
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function setOgDescription(?string $ogDescription): self
    {
        $this->ogDescription = $ogDescription;
        return $this;
    }

    public function getOgType(): ?string
    {
        return $this->ogType;
    }

    public function setOgType(?string $ogType): self
    {
        $this->ogType = $ogType;
        return $this;
    }

    public function getOgImage(): ?string
    {
        return $this->ogImage;
    }

    public function setOgImage(string $ogImage): self
    {
        $this->ogImage = $ogImage;
        return $this;
    }

    public function getOgImagePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->getOgImage();
    }

    public function setOgImagePath(string $ogImagePath): self
    {
        $this->ogImagePath = $ogImagePath;
        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getMetaKeyword(): ?string
    {
        return $this->metaKeyword;
    }

    public function setMetaKeyword(?string $metaKeyword): self
    {
        $this->metaKeyword = $metaKeyword;
        return $this;
    }

    public function getFocusKeyPhrase(): ?string
    {
        return $this->focusKeyPhrase;
    }

    public function setFocusKeyPhrase(?string $focusKeyPhrase): self
    {
        $this->focusKeyPhrase = $focusKeyPhrase;
        return $this;
    }

    public function getKeyPhraseSynonyms(): ?string
    {
        return $this->keyPhraseSynonyms;
    }

    public function setKeyPhraseSynonyms(?string $keyPhraseSynonyms): self
    {
        $this->keyPhraseSynonyms = $keyPhraseSynonyms;
        return $this;
    }

    public function getSeoSchema(): ?string
    {
        return $this->seoSchema;
    }

    public function setSeoSchema(?string $secSchema): self
    {
        $this->seoSchema = $secSchema;

        return $this;
    }


    public function getArticleCreateDateTime(): ?DateTimeInterface
    {
        return $this->articleCreateDateTime;
    }

    public function setArticleCreateDateTime(DateTimeInterface $articleCreateDateTime): self
    {
        $this->articleCreateDateTime = $articleCreateDateTime;

        return $this;
    }

    public function getArticleCreatedBy(): ?AppUser
    {
        return $this->articleCreatedBy;
    }

    public function setArticleCreatedBy(?AppUser $articleCreatedBy): self
    {
        $this->articleCreatedBy = $articleCreatedBy;

        return $this;
    }

    public function getArticleUpdateDateTime(): ?DateTimeInterface
    {
        return $this->articleUpdateDateTime;
    }

    public function setArticleUpdateDateTime(?DateTimeInterface $articleUpdateDateTime): self
    {
        $this->articleUpdateDateTime = $articleUpdateDateTime;

        return $this;
    }

    public function getArticleUpdatedBy(): ?AppUser
    {
        return $this->articleUpdatedBy;
    }

    public function setArticleUpdatedBy(?AppUser $articleUpdatedBy): self
    {
        $this->articleUpdatedBy = $articleUpdatedBy;

        return $this;
    }

    public function getLocationLatitude(): ?string
    {
        return $this->locationLatitude;
    }

    public function setLocationLatitude(?string $locationLatitude): self
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->locationLongitude;
    }

    public function setLocationLongitude(?string $locationLongitude): self
    {
        $this->locationLongitude = $locationLongitude;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(string $cityName): self
    {
        $this->cityName = $cityName;
        return $this;
    }

    public function getLocationName(): ?string
    {
        return $this->locationName;
    }

    public function setLocationName(string $locationName): self
    {
        $this->locationName = $locationName;
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
        return $this->articleTitle;
    }

    /**
     * @return Collection|CmsArticleComment[]
     */
    public function getCmsArticleComment(): Collection
    {
        return $this->cmsArticleComment;
    }

    public function addCmsArticleComment(CmsArticleComment $cmsArticleComment): self
    {
        if (!$this->cmsArticleComment->contains($cmsArticleComment)) {
            $this->cmsArticleComment[] = $cmsArticleComment;
            $cmsArticleComment->setCmsArticle($this);
        }

        return $this;
    }

    public function removeCmsArticleComment(CmsArticleComment $cmsArticleComment): self
    {
        if ($this->cmsArticleComment->contains($cmsArticleComment)) {
            $this->cmsArticleComment->removeElement($cmsArticleComment);
            // set the owning side to null (unless already changed)
            if ($cmsArticleComment->getCmsArticle() === $this) {
                $cmsArticleComment->setCmsArticle(null);
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

    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }

    public function getMstState(): ?MstState
    {
        return $this->mstState;
    }

    public function setMstState(?MstState $mstState): self
    {
        $this->mstState = $mstState;

        return $this;
    }

    public function getMstCity(): ?MstCity
    {
        return $this->mstCity;
    }

    public function setMstCity(?MstCity $mstCity): self
    {
        $this->mstCity = $mstCity;

        return $this;
    }

    /**
     * @return Collection|CmsArticleContent[]
     */
    public function getCmsArticleContent(): Collection
    {
        return $this->cmsArticleContent;
    }

    public function addCmsArticleContent(CmsArticleContent $cmsArticleContent): self
    {
        if (!$this->cmsArticleContent->contains($cmsArticleContent)) {
            $this->cmsArticleContent[] = $cmsArticleContent;
            $cmsArticleContent->setCmsArticle($this);
        }

        return $this;
    }

    public function removeCmsArticleContent(CmsArticleContent $cmsArticleContent): self
    {
        if ($this->cmsArticleContent->contains($cmsArticleContent)) {
            $this->cmsArticleContent->removeElement($cmsArticleContent);
            // set the owning side to null (unless already changed)
            if ($cmsArticleContent->getCmsArticle() === $this) {
                $cmsArticleContent->setCmsArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MstAreaInterest[]
     */
    public function getMstAreaInterest(): Collection
    {
        return $this->mstAreaInterest;
    }

    public function addMstAreaInterest(MstAreaInterest $mstAreaInterest): self
    {
        if (!$this->mstAreaInterest->contains($mstAreaInterest)) {
            $this->mstAreaInterest[] = $mstAreaInterest;
        }

        return $this;
    }

    public function removeMstAreaInterest(MstAreaInterest $mstAreaInterest): self
    {
        if ($this->mstAreaInterest->contains($mstAreaInterest)) {
            $this->mstAreaInterest->removeElement($mstAreaInterest);
        }

        return $this;
    }

    public function getChangeMakerAppUser(): ?AppUser
    {
        return $this->changeMakerAppUser;
    }

    public function setChangeMakerAppUser(?AppUser $changeMakerAppUser): self
    {
        $this->changeMakerAppUser = $changeMakerAppUser;

        return $this;
    }

}
