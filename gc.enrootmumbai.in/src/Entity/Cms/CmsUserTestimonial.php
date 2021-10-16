<?php

namespace App\Entity\Cms;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Cms\CmsUserTestimonialRepository")
 * @ORM\Table("cmsusertestimonial")
 */
class CmsUserTestimonial
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
     * @ORM\Column(type="string", length=50)
     */
    private $testimonialFor;

    /**
     * @ORM\Column(type="text")
     */
    private $testimonialDetail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDateTime;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

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

    public function getTestimonialFor(): ?string
    {
        return $this->testimonialFor;
    }

    public function setTestimonialFor(string $testimonialFor): self
    {
        $this->testimonialFor = $testimonialFor;

        return $this;
    }

    public function getTestimonialDetail(): ?string
    {
        return $this->testimonialDetail;
    }

    public function setTestimonialDetail(string $testimonialDetail): self
    {
        $this->testimonialDetail = $testimonialDetail;

        return $this;
    }

    public function getCreateDateTime(): ?\DateTimeInterface
    {
        return $this->createDateTime;
    }

    public function setCreateDateTime(\DateTimeInterface $createDateTime): self
    {
        $this->createDateTime = $createDateTime;

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




}
