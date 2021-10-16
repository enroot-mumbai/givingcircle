<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstTypeOfOrganization;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnOrganizationDetailsRepository;
use App\Service\FileUploaderHelper;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrnOrganizationDetailsRepository::class)
 * @ORM\Table("trnorganizationdetails")
 */
class TrnOrganizationDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aboutOrganization;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $registrationCertificateTrustDeed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $incorporatedOnDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstTypeOfOrganization::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstTypeOfOrganization;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $orgCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoFilePath;

    /**
     * @ORM\ManyToOne(targetEntity=MstAreaInterest::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstAreaInterest;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $registrationNo80G;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $registrationDate80G;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class, inversedBy="trnOrganizationDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $organizationName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $organizationLogo;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $organizationLogoFilePath;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAboutOrganization(): ?string
    {
        return $this->aboutOrganization;
    }

    public function setAboutOrganization(?string $aboutOrganization): self
    {
        $this->aboutOrganization = $aboutOrganization;

        return $this;
    }

    public function getRegistrationCertificateTrustDeed(): ?string
    {
        return $this->registrationCertificateTrustDeed;
    }

    public function setRegistrationCertificateTrustDeed(?string $registrationCertificateTrustDeed): self
    {
        $this->registrationCertificateTrustDeed = $registrationCertificateTrustDeed;

        return $this;
    }

    public function getIncorporatedOnDate(): ?DateTimeInterface
    {
        return $this->incorporatedOnDate;
    }

    public function setIncorporatedOnDate(?DateTimeInterface $incorporatedOnDate): self
    {
        $this->incorporatedOnDate = $incorporatedOnDate;

        return $this;
    }

    public function getCreatedOn(): ?DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(?DateTimeInterface $createdOn): self
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

    public function getMstTypeOfOrganization(): ?MstTypeOfOrganization
    {
        return $this->mstTypeOfOrganization;
    }

    public function setMstTypeOfOrganization(?MstTypeOfOrganization $mstTypeOfOrganization): self
    {
        $this->mstTypeOfOrganization = $mstTypeOfOrganization;

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

    public function getLogoFilePath(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'. $this->logoFilePath;
    }

    public function setLogoFilePath(?string $logoFilePath): self
    {
        $this->logoFilePath = $logoFilePath;

        return $this;
    }

    public function getMstAreaInterest(): ?MstAreaInterest
    {
        return $this->mstAreaInterest;
    }

    public function setMstAreaInterest(?MstAreaInterest $mstAreaInterest): self
    {
        $this->mstAreaInterest = $mstAreaInterest;

        return $this;
    }

    public function getRegistrationNo80G(): ?string
    {
        return $this->registrationNo80G;
    }

    public function setRegistrationNo80G(?string $registrationNo80G): self
    {
        $this->registrationNo80G = $registrationNo80G;

        return $this;
    }

    public function getRegistrationDate80G(): ?DateTimeInterface
    {
        return $this->registrationDate80G;
    }

    public function setRegistrationDate80G(?DateTimeInterface $registrationDate80G): self
    {
        $this->registrationDate80G = $registrationDate80G;

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

    public function __toString(){
        $return = '';
        return $return;
    }

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(string $organizationName): self
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getOrganizationLogo(): ?string
    {
        return $this->organizationLogo;
    }

    public function setOrganizationLogo(?string $organizationLogo): self
    {
        $this->organizationLogo = $organizationLogo;

        return $this;
    }

    public function getOrganizationLogoFilePath(): ?string
    {
        return FileUploaderHelper::PRIVATE_FILE.'/'.$this->getOrganizationLogo();
    }

    public function setOrganizationLogoFilePath(?string $organizationLogoFilePath): self
    {
        $this->organizationLogoFilePath = $organizationLogoFilePath;

        return $this;
    }
}
