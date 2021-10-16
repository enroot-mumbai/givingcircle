<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstSourceOfInformation;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterSourceOfInformationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterSourceOfInformationRepository::class)
 * @ORM\Table("trnvoluntersourceofinformation")
 */
class TrnVolunterSourceOfInformation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TrnVolunterDetail::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterDetail;

    /**
     * @ORM\ManyToOne(targetEntity=MstSourceOfInformation::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstSourceOfInformation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMstSourceOfInformation(): ?MstSourceOfInformation
    {
        return $this->mstSourceOfInformation;
    }

    public function setMstSourceOfInformation(?MstSourceOfInformation $mstSourceOfInformation): self
    {
        $this->mstSourceOfInformation = $mstSourceOfInformation;

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

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

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
