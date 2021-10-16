<?php

namespace App\Entity\Transaction;

use App\Entity\SystemApp\AppUser;
use App\Repository\Transaction\TrnAppUserContactsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrnAppUserContactsRepository::class)
 * @ORM\Table("trnappusercontacts")
 */
class TrnAppUserContacts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AppUser::class, inversedBy="trnAppUserContacts")
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $mobileCountryCode;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $salutation;

    /**
     * @ORM\OneToMany(targetEntity=TrnCircleInvitations::class, mappedBy="trnAppUserContacts")
     */
    private $trnCircleInvitations;

    public function __construct()
    {
        $this->trnCircleInvitations = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMobileCountryCode(): ?string
    {
        return $this->mobileCountryCode;
    }

    public function setMobileCountryCode(?string $mobileCountryCode): self
    {
        $this->mobileCountryCode = $mobileCountryCode;

        return $this;
    }

    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(?string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(?string $salutation): self
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * @return Collection|TrnCircleInvitations[]
     */
    public function getTrnCircleInvitations(): Collection
    {
        return $this->trnCircleInvitations;
    }

    public function addTrnCircleInvitation(TrnCircleInvitations $trnCircleInvitation): self
    {
        if (!$this->trnCircleInvitations->contains($trnCircleInvitation)) {
            $this->trnCircleInvitations[] = $trnCircleInvitation;
            $trnCircleInvitation->setTrnAppUserContacts($this);
        }

        return $this;
    }

    public function removeTrnCircleInvitation(TrnCircleInvitations $trnCircleInvitation): self
    {
        if ($this->trnCircleInvitations->removeElement($trnCircleInvitation)) {
            // set the owning side to null (unless already changed)
            if ($trnCircleInvitation->getTrnAppUserContacts() === $this) {
                $trnCircleInvitation->setTrnAppUserContacts(null);
            }
        }

        return $this;
    }
}
