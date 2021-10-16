<?php

namespace App\Entity\SystemApp;

use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnAppUserContacts;
use App\Entity\Transaction\TrnBankDetails;
use App\Entity\Transaction\TrnOrganizationDetails;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
use App\Entity\Transaction\TrnVolunterDetail;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppUserRepository")
 * @ORM\Table("appuser",
 *     indexes={@ORM\Index(name="Name_Active_idx", columns={"userName","isActive"})}
 * )
 *
 */
class AppUser implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank(message="user.userName.not_blank")
     * @Groups({"read", "write"})
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private $userPassword;

    /**
     * @ORM\Column(type="json")
     * @Groups({"read", "write"})
     */
    private $userRole = [];

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $userSessionId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $userLastLogin;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $userResetPasswordToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $userResetPasswordTokenExpiry;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $userCreationToken;

    /**
     * @ORM\Column(type="guid")
     */
    private $rowId;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read"})
     */
    private $isActive;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SystemApp\AppUserInfo", mappedBy="appUser", cascade={"persist", "remove"})
     */
    public $appUserInfo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SystemApp\AppUserCategory", inversedBy="appUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUserCategory;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrganizationDetails::class, mappedBy="appUser", cascade={"persist", "remove"})
     */
    private $trnOrganizationDetails;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     */
    private $mstStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $userCreationDateTime;

    /**
     * @ORM\OneToMany(targetEntity=TrnBankDetails::class, mappedBy="appUserMemberDetails", cascade={"persist", "remove"})
     */
    private $trnBankDetails;

    /**
     * @ORM\OneToMany(targetEntity=TrnOrganizationUploadDocuments::class, mappedBy="appUserMemberDetails", cascade={"persist", "remove"})
     */
    private $trnOrganizationUploadDocuments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googleId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasonToReject;

    /**
     * @ORM\OneToOne(targetEntity=TrnVolunterDetail::class, mappedBy="appUser", cascade={"persist", "remove"})
     */
    private $trnVolunterDetail;

    /**
     * @ORM\OneToMany(targetEntity=TrnAppUserContacts::class, mappedBy="appUser")
     */
    private $trnAppUserContacts;

    public function __construct()
    {
        $this->trnOrganizationDetails = new ArrayCollection();
        $this->trnBankDetails = new ArrayCollection();
        $this->trnOrganizationUploadDocuments = new ArrayCollection();
        $this->trnAppUserContacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): self
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    public function getUserRole(): ?array
    {
        return $this->userRole;
    }

    public function setUserRole(array $userRole): self
    {
        $this->userRole = $userRole;

        return $this;
    }

    public function getUserSessionId(): ?string
    {
        return $this->userSessionId;
    }

    public function setUserSessionId(?string $userSessionId): self
    {
        $this->userSessionId = $userSessionId;

        return $this;
    }

    public function getUserLastLogin(): ?DateTimeInterface
    {
        return $this->userLastLogin;
    }

    public function setUserLastLogin(?DateTimeInterface $userLastLogin): self
    {
        $this->userLastLogin = $userLastLogin;

        return $this;
    }

    public function getUserResetPasswordToken(): ?string
    {
        return $this->userResetPasswordToken;
    }

    public function setUserResetPasswordToken(?string $userResetPasswordToken): self
    {
        $this->userResetPasswordToken = $userResetPasswordToken;

        return $this;
    }

    public function getUserResetPasswordTokenExpiry(): ?DateTimeInterface
    {
        return $this->userResetPasswordTokenExpiry;
    }

    public function setUserResetPasswordTokenExpiry(?DateTimeInterface $userResetPasswordTokenExpiry): self
    {
        $this->userResetPasswordTokenExpiry = $userResetPasswordTokenExpiry;

        return $this;
    }

    public function getUserCreationToken(): ?string
    {
        return $this->userCreationToken;
    }

    public function setUserCreationToken(?string $userCreationToken): self
    {
        $this->userCreationToken = $userCreationToken;

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
        return $this->getAppUserInfo()->getMstSalutation().' '.$this->getAppUserInfo()->getUserFirstName().' '.$this->getAppUserInfo()->getUserMiddleName().' '.$this->getAppUserInfo()->getUserLastName();
    }

    public function getAppUserDetails()
    {
        $strOrgDetails = " Ind ";
        $objOrg =  $this->getTrnOrganizationDetails();
        if(!empty($objOrg) && !empty($objOrg[0])) {
            $strOrgDetails = " Org ". $objOrg[0]->getOrganizationName();
        }
        return $this->getAppUserInfo()->getMstSalutation().' '.$this->getAppUserInfo()->getUserFirstName().' '
            .$this->getAppUserInfo()->getUserMiddleName().' '.$this->getAppUserInfo()->getUserLastName(). ' - '.$strOrgDetails;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getRoles(): ?array
    {
        $userRole =  $this->userRole;
        // guarantee every user at least has ROLE_USER
        $userRole[] = 'ROLE_APP_USER';
        return array_unique($userRole);

    }

    public function setRoles(array $roles): self
    {
        $this->userRole = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setPassword(string $password): self
    {
        $this->userPassword = $password;

        return $this;
    }

    public function getAppUserInfo(): ?AppUserInfo
    {
        return $this->appUserInfo;
    }

    public function setAppUserInfo(AppUserInfo $appUserInfo): self
    {
        $this->appUserInfo = $appUserInfo;

        // set the owning side of the relation if necessary
        if ($appUserInfo->getAppUser() !== $this) {
            $appUserInfo->setAppUser($this);
        }

        return $this;
    }

    public function getAppUserCategory(): ?AppUserCategory
    {
        return $this->appUserCategory;
    }

    public function setAppUserCategory(?AppUserCategory $appUserCategory): self
    {
        $this->appUserCategory = $appUserCategory;

        return $this;
    }

    /**
     * @return Collection|TrnOrganizationDetails[]
     */
    public function getTrnOrganizationDetails(): Collection
    {
        return $this->trnOrganizationDetails;
    }

    public function addTrnOrganizationDetail(TrnOrganizationDetails $trnOrganizationDetail): self
    {
        if (!$this->trnOrganizationDetails->contains($trnOrganizationDetail)) {
            $this->trnOrganizationDetails[] = $trnOrganizationDetail;
            $trnOrganizationDetail->setAppUser($this);
        }

        return $this;
    }

    public function removeTrnOrganizationDetail(TrnOrganizationDetails $trnOrganizationDetail): self
    {
        if ($this->trnOrganizationDetails->contains($trnOrganizationDetail)) {
            $this->trnOrganizationDetails->removeElement($trnOrganizationDetail);
            // set the owning side to null (unless already changed)
            if ($trnOrganizationDetail->getAppUser() === $this) {
                $trnOrganizationDetail->setAppUser(null);
            }
        }

        return $this;
    }

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

        return $this;
    }

    public function getUserCreationDateTime(): ?\DateTimeInterface
    {
        return $this->userCreationDateTime;
    }

    public function setUserCreationDateTime(?\DateTimeInterface $userCreationDateTime): self
    {
        $this->userCreationDateTime = $userCreationDateTime;

        return $this;
    }

    /**
     * @return Collection|TrnBankDetails[]
     */
    public function getTrnBankDetails(): Collection
    {
        return $this->trnBankDetails;
    }

    public function addTrnBankDetail(TrnBankDetails $trnBankDetail): self
    {
        if (!$this->trnBankDetails->contains($trnBankDetail)) {
            $this->trnBankDetails[] = $trnBankDetail;
            $trnBankDetail->setAppUserMemberDetails($this);
        }

        return $this;
    }

    public function removeTrnBankDetail(TrnBankDetails $trnBankDetail): self
    {
        if ($this->trnBankDetails->contains($trnBankDetail)) {
            $this->trnBankDetails->removeElement($trnBankDetail);
            // set the owning side to null (unless already changed)
            if ($trnBankDetail->getAppUserMemberDetails() === $this) {
                $trnBankDetail->setAppUserMemberDetails(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrnOrganizationUploadDocuments[]
     */
    public function getTrnOrganizationUploadDocuments(): Collection
    {
        return $this->trnOrganizationUploadDocuments;
    }

    public function addTrnOrganizationUploadDocument(TrnOrganizationUploadDocuments $trnOrganizationUploadDocument): self
    {
        if (!$this->trnOrganizationUploadDocuments->contains($trnOrganizationUploadDocument)) {
            $this->trnOrganizationUploadDocuments[] = $trnOrganizationUploadDocument;
            $trnOrganizationUploadDocument->setAppUserMemberDetails($this);
        }

        return $this;
    }

    public function removeTrnOrganizationUploadDocument(TrnOrganizationUploadDocuments $trnOrganizationUploadDocument): self
    {
        if ($this->trnOrganizationUploadDocuments->contains($trnOrganizationUploadDocument)) {
            $this->trnOrganizationUploadDocuments->removeElement($trnOrganizationUploadDocument);
            // set the owning side to null (unless already changed)
            if ($trnOrganizationUploadDocument->getAppUserMemberDetails() === $this) {
                $trnOrganizationUploadDocument->setAppUserMemberDetails(null);
            }
        }

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getReasonToReject(): ?string
    {
        return $this->reasonToReject;
    }

    public function setReasonToReject(?string $reasonToReject): self
    {
        $this->reasonToReject = $reasonToReject;

        return $this;
    }

    public function getTrnVolunterDetail(): ?TrnVolunterDetail
    {
        return $this->trnVolunterDetail;
    }

    public function setTrnVolunterDetail(?TrnVolunterDetail $trnVolunterDetail): self
    {
        // unset the owning side of the relation if necessary
        if ($trnVolunterDetail === null && $this->trnVolunterDetail !== null) {
            $this->trnVolunterDetail->setAppUser(null);
        }

        // set the owning side of the relation if necessary
        if ($trnVolunterDetail !== null && $trnVolunterDetail->getAppUser() !== $this) {
            $trnVolunterDetail->setAppUser($this);
        }

        $this->trnVolunterDetail = $trnVolunterDetail;

        return $this;
    }

    /**
     * @return Collection|TrnAppUserContacts[]
     */
    public function getTrnAppUserContacts(): Collection
    {
        return $this->trnAppUserContacts;
    }

    public function addTrnAppUserContact(TrnAppUserContacts $trnAppUserContact): self
    {
        if (!$this->trnAppUserContacts->contains($trnAppUserContact)) {
            $this->trnAppUserContacts[] = $trnAppUserContact;
            $trnAppUserContact->setAppUser($this);
        }

        return $this;
    }

    public function removeTrnAppUserContact(TrnAppUserContacts $trnAppUserContact): self
    {
        if ($this->trnAppUserContacts->removeElement($trnAppUserContact)) {
            // set the owning side to null (unless already changed)
            if ($trnAppUserContact->getAppUser() === $this) {
                $trnAppUserContact->setAppUser(null);
            }
        }

        return $this;
    }
}
