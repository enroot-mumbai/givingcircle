<?php

namespace App\Entity\SystemApp;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstGender;
use App\Entity\Master\MstMaritalStatus;
use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\Organization\OrgCompanyOffice;
use App\Service\FileUploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SystemApp\AppUserInfoRepository")
 * @ORM\Table("appuserinfo")
 */
class AppUserInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SystemApp\AppUser", inversedBy="appUserInfo", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $appUser;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank(message="user.userEmail.not_blank")
     * @Assert\Email(message="comment.invalid_email")
     */
    private $userEmail;

    /**
     * @ORM\ManyToOne(targetEntity=MstSalutation::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstSalutation;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     *@Assert\NotBlank(message="user.userFirstName.not_blank")
     */
    private $userFirstName;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $userMiddleName;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\NotBlank(message="user.userLastName.not_blank")
     */
    private $userLastName;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Your mobile number must be {{ limit }} characters long",
     *      maxMessage = "Yourmobile number cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $userMobileNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $userAvatarImage;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $userAvatarImagePath;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization\OrgCompany", inversedBy="appUserInfo")
     */
    private $orgCompany;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization\OrgCompanyOffice", inversedBy="appUserInfo")
     */
    private $orgCompanyOffice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address2;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $pincode;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $pancardNumber;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $mobileCountryCode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSubscribedToNewLetter;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     */
    private $mstCity;

    /**
     * @ORM\ManyToOne(targetEntity=MstUserMemberType::class)
     */
    private $mstUserMemberType;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\ManyToOne(targetEntity=MstState::class)
     */
    private $mstState;

    /**
     * @ORM\ManyToOne(targetEntity=MstCountry::class)
     */
    private $mstCountry;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userIpAddress;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $locationLongitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $googlePlusLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitterHandleLink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePic;

    /**
     * @ORM\ManyToMany(targetEntity=MstSkillSet::class)
     */
    private $mstSkillSet;

    /**
     * @ORM\ManyToMany(targetEntity=MstAreaInterest::class)
     */
    private $mstAreaInterest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundProfile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundProfilePath;

    public function __construct()
    {
        $this->mstSkillSet = new ArrayCollection();
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

    public function setAppUser(AppUser $appUser): self
    {
        $this->appUser = $appUser;
        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function getMstSalutation(): ?MstSalutation
    {
        return $this->mstSalutation;
    }

    public function setMstSalutation(?MstSalutation $mstSalutation): self
    {
        $this->mstSalutation = $mstSalutation;
        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(?string $userFirstName): self
    {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    public function getUserMiddleName(): ?string
    {
        return $this->userMiddleName;
    }

    public function setUserMiddleName(?string $userMiddleName): self
    {
        $this->userMiddleName = $userMiddleName;
        return $this;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(?string $userLastName): self
    {
        $this->userLastName = $userLastName;
        return $this;
    }

    public function getUserMobileNumber(): ?int
    {
        return $this->userMobileNumber;
    }

    public function setUserMobileNumber(int $userMobileNumber): self
    {
        $this->userMobileNumber = $userMobileNumber;
        return $this;
    }

    public function getUserAvatarImage(): ?string
    {
        return $this->userAvatarImage;
    }

    public function setUserAvatarImage(?string $userAvatarImage): self
    {
        $this->userAvatarImage = $userAvatarImage;
        return $this;
    }

    public function getUserAvatarImagePath(): ?string
    {
        return FileUploaderHelper::PRIVATE_FILE.'/'.$this->getUserAvatarImage();
    }

    public function setUserAvatarImagePath(?string $userAvatarImagePath): self
    {
        $this->userAvatarImagePath = $userAvatarImagePath;
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

    public function getOrgCompanyOffice(): ?OrgCompanyOffice
    {
        return $this->orgCompanyOffice;
    }

    public function setOrgCompanyOffice(?OrgCompanyOffice $orgCompanyOffice): self
    {
        $this->orgCompanyOffice = $orgCompanyOffice;
        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPincode(): ?string
    {
        return $this->pincode;
    }

    public function setPincode(?string $pincode): self
    {
        $this->pincode = $pincode;

        return $this;
    }

    public function getPancardNumber(): ?string
    {
        return $this->pancardNumber;
    }

    public function setPancardNumber(?string $pancardNumber): self
    {
        $this->pancardNumber = $pancardNumber;

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

    public function getIsSubscribedToNewLetter(): ?bool
    {
        return $this->isSubscribedToNewLetter;
    }

    public function setIsSubscribedToNewLetter(?bool $isSubscribedToNewLetter): self
    {
        $this->isSubscribedToNewLetter = $isSubscribedToNewLetter;

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

    public function getMstUserMemberType(): ?MstUserMemberType
    {
        return $this->mstUserMemberType;
    }

    public function setMstUserMemberType(?MstUserMemberType $mstUserMemberType): self
    {
        $this->mstUserMemberType = $mstUserMemberType;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getMstCountry(): ?MstCountry
    {
        return $this->mstCountry;
    }

    public function setMstCountry(?MstCountry $mstCountry): self
    {
        $this->mstCountry = $mstCountry;

        return $this;
    }

    public function getName(){
        $strName = $this->userFirstName. ' ' . $this->userMiddleName. ' '. $this->userLastName;
        return str_ireplace('  ', ' ', $strName);
    }

    public function getMobWithCountryCode(){
        $strMobileWithCountryCode = $this->mobileCountryCode.'-'.$this->userMobileNumber;
        return trim($strMobileWithCountryCode,'-');
    }

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

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

    public function getFacebookLink(): ?string
    {
        return $this->facebookLink;
    }

    public function setFacebookLink(?string $facebookLink): self
    {
        $this->facebookLink = $facebookLink;

        return $this;
    }

    public function getGooglePlusLink(): ?string
    {
        return $this->googlePlusLink;
    }

    public function setGooglePlusLink(?string $googlePlusLink): self
    {
        $this->googlePlusLink = $googlePlusLink;

        return $this;
    }

    public function getTwitterHandleLink(): ?string
    {
        return $this->twitterHandleLink;
    }

    public function setTwitterHandleLink(?string $twitterHandleLink): self
    {
        $this->twitterHandleLink = $twitterHandleLink;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return FileUploaderHelper::PUBLIC_FILE.'/'.$this->profilePic;
    }

    public function setProfilePic(?string $profilePic): self
    {
        $this->profilePic = $profilePic;

        return $this;
    }

    /**
     * @return Collection|MstSkillSet[]
     */
    public function getMstSkillSet(): Collection
    {
        return $this->mstSkillSet;
    }

    public function addMstSkillSet(MstSkillSet $mstSkillSet): self
    {
        if (!$this->mstSkillSet->contains($mstSkillSet)) {
            $this->mstSkillSet[] = $mstSkillSet;
        }

        return $this;
    }

    public function removeMstSkillSet(MstSkillSet $mstSkillSet): self
    {
        if ($this->mstSkillSet->contains($mstSkillSet)) {
            $this->mstSkillSet->removeElement($mstSkillSet);
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

    public function getBackgroundProfile(): ?string
    {
        return $this->backgroundProfile;
    }

    public function setBackgroundProfile(?string $backgroundProfile): self
    {
        $this->backgroundProfile = $backgroundProfile;

        return $this;
    }

    public function getBackgroundProfilePath(): ?string
    {
        return FileUploaderHelper::PRIVATE_FILE.'/'.$this->getBackgroundProfile();
    }

    public function setBackgroundProfilePath(?string $backgroundProfilePath): self
    {
        $this->backgroundProfilePath = $backgroundProfilePath;

        return $this;
    }

}
