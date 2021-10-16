<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstSkillSet;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnVolunterCircleEventSkillSetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnVolunterCircleEventSkillSetRepository::class)
 * @ORM\Table("trnvoluntercircleeventskillset")
 */
class TrnVolunterCircleEventSkillSet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=TrnVolunterCircleEventDetails::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trnVolunterCircleEventDetails;

    /**
     * @ORM\ManyToOne(targetEntity=MstSkillSet::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mstSkillSet;

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

    public function getTrnVolunterCircleEventDetails(): ?TrnVolunterCircleEventDetails
    {
        return $this->trnVolunterCircleEventDetails;
    }

    public function setTrnVolunterCircleEventDetails(?TrnVolunterCircleEventDetails $trnVolunterCircleEventDetails): self
    {
        $this->trnVolunterCircleEventDetails = $trnVolunterCircleEventDetails;

        return $this;
    }

    public function getMstSkillSet(): ?MstSkillSet
    {
        return $this->mstSkillSet;
    }

    public function setMstSkillSet(?MstSkillSet $mstSkillSet): self
    {
        $this->mstSkillSet = $mstSkillSet;

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
