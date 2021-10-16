<?php

namespace App\Entity\Form;

use App\Entity\Master\MstCity;
use App\Entity\Organization\OrgCompany;
use App\Repository\Form\FormReportRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FormReportRepository::class)
 * @ORM\Table("formreport")
 */
class FormReport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $reportFor;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Assert\PositiveOrZero(
     *  message="The number {{}} should be numerical"
     * )
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cityName;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     */
    private $mstCity;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $otherFirstName;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $otherLastName;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $otherEmailAddress;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Assert\PositiveOrZero(
     *  message="The number {{}} should be numerical"
     * )
     */
    private $otherMobileNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $otherCityName;

    /**
     * @ORM\ManyToOne(targetEntity=MstCity::class)
     */
    private $mstCityOther;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $uploadFile;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $uploadFilePath;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $yourInspire;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $otherInspire;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $digitalPresence = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $messageDetail;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $remarks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDateTime;

    /**
     * @ORM\ManyToOne(targetEntity=OrgCompany::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $orgCompany;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getreportFor(): ?string
    {
        return $this->reportFor;
    }

    public function setreportFor(string $reportFor): self
    {
        $this->reportFor = $reportFor;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

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

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(?string $cityName): self
    {
        $this->cityName = $cityName;

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

    public function getOtherFirstName(): ?string
    {
        return $this->otherFirstName;
    }

    public function setOtherFirstName(?string $otherFirstName): self
    {
        $this->otherFirstName = $otherFirstName;

        return $this;
    }

    public function getOtherLastName(): ?string
    {
        return $this->otherLastName;
    }

    public function setOtherLastName(?string $otherLastName): self
    {
        $this->otherLastName = $otherLastName;

        return $this;
    }

    public function getOtherEmailAddress(): ?string
    {
        return $this->otherEmailAddress;
    }

    public function setOtherEmailAddress(?string $otherEmailAddress): self
    {
        $this->otherEmailAddress = $otherEmailAddress;

        return $this;
    }

    public function getOtherMobileNumber(): ?string
    {
        return $this->otherMobileNumber;
    }

    public function setOtherMobileNumber(?string $otherMobileNumber): self
    {
        $this->otherMobileNumber = $otherMobileNumber;

        return $this;
    }

    public function getOtherCityName(): ?string
    {
        return $this->otherCityName;
    }

    public function setOtherCityName(?string $otherCityName): self
    {
        $this->otherCityName = $otherCityName;

        return $this;
    }

    public function getMstCityOther(): ?MstCity
    {
        return $this->mstCityOther;
    }

    public function setMstCityOther(?MstCity $mstCityOther): self
    {
        $this->mstCityOther = $mstCityOther;

        return $this;
    }

    public function getUploadFile(): ?string
    {
        return $this->uploadFile;
    }

    public function setUploadFile(?string $uploadFile): self
    {
        $this->uploadFile = $uploadFile;

        return $this;
    }

    public function getUploadFilePath(): ?string
    {
        return $this->uploadFilePath;
    }

    public function setUploadFilePath(?string $uploadFilePath): self
    {
        $this->uploadFilePath = $uploadFilePath;

        return $this;
    }

    public function getYourInspire(): ?string
    {
        return $this->yourInspire;
    }

    public function setYourInspire(?string $yourInspire): self
    {
        $this->yourInspire = $yourInspire;

        return $this;
    }

    public function getOtherInspire(): ?string
    {
        return $this->otherInspire;
    }

    public function setOtherInspire(?string $otherInspire): self
    {
        $this->otherInspire = $otherInspire;

        return $this;
    }

    public function getDigitalPresence(): ?array
    {
        return $this->digitalPresence;
    }

    public function setDigitalPresence(?array $digitalPresence): self
    {
        $this->digitalPresence = $digitalPresence;

        return $this;
    }

    public function getMessageDetail(): ?string
    {
        return $this->messageDetail;
    }

    public function setMessageDetail(?string $messageDetail): self
    {
        $this->messageDetail = $messageDetail;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getCreateDateTime(): ?DateTimeInterface
    {
        return $this->createDateTime;
    }

    public function setCreateDateTime(DateTimeInterface $createDateTime): self
    {
        $this->createDateTime = $createDateTime;

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
