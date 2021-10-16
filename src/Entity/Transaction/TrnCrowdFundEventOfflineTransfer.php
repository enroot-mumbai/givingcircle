<?php

namespace App\Entity\Transaction;

use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstStatus;
use App\Repository\Transaction\TrnCrowdFundEventOfflineTransferRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TrnCrowdFundEventOfflineTransferRepository::class)
 * @ORM\Table("trncrowdfundeventofflinetransfer")
 */
class TrnCrowdFundEventOfflineTransfer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $bankTransactionId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amountDonated;

    /**
     * @ORM\ManyToOne(targetEntity=MstSalutation::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $mstSalutation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *@Assert\NotBlank(message="user.userFirstName.not_blank")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="user.userLastName.not_blank")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $mobileCountryCode;

    /**
     * @ORM\Column(nullable=true)
     *
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Your mobile number must be {{ limit }} characters long",
     *      maxMessage = "Yourmobile number cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAnonymousDonation;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=MstStatus::class)
     */
    private $mstStatus;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCrowdFundEvent::class, inversedBy="trnCrowdFundEventOfflineTransfer")
     */
    private $trnCrowdFundEvent;

    /**
     * @ORM\ManyToOne(targetEntity=TrnCircleEvents::class, inversedBy="trncrowdfundeventofflinetransfer")
     */
    private $trnCircleEvent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userIpAddress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBankTransactionId(): ?string
    {
        return $this->bankTransactionId;
    }

    public function setBankTransactionId(?string $bankTransactionId): self
    {
        $this->bankTransactionId = $bankTransactionId;

        return $this;
    }

    public function getAmountDonated(): ?string
    {
        return $this->amountDonated;
    }

    public function setAmountDonated(?string $amountDonated): self
    {
        $this->amountDonated = $amountDonated;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
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

    public function getMobileNumber(): ?int
    {
        return $this->mobileNumber;
    }

    public function setMobileNumber(int $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;
        return $this;
    }

    public function getIsAnonymousDonation(): ?bool
    {
        return $this->isAnonymousDonation;
    }

    public function setIsAnonymousDonation(?bool $isAnonymousDonation): self
    {
        $this->isAnonymousDonation = $isAnonymousDonation;

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

    public function getMstStatus(): ?MstStatus
    {
        return $this->mstStatus;
    }

    public function setMstStatus(?MstStatus $mstStatus): self
    {
        $this->mstStatus = $mstStatus;

        return $this;
    }

    public function getTrnCrowdFundEvent(): ?TrnCrowdFundEvent
    {
        return $this->trnCrowdFundEvent;
    }

    public function setTrnCrowdFundEvent(?TrnCrowdFundEvent $trnCrowdFundEvent): self
    {
        $this->trnCrowdFundEvent = $trnCrowdFundEvent;

        return $this;
    }

    public function getTrnCircleEvent(): ?TrnCircleEvents
    {
        return $this->trnCircleEvent;
    }

    public function setTrnCircleEvent(?TrnCircleEvents $trnCircleEvent): self
    {
        $this->trnCircleEvent = $trnCircleEvent;

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

    public function getUserIpAddress(): ?string
    {
        return $this->userIpAddress;
    }

    public function setUserIpAddress(string $userIpAddress): self
    {
        $this->userIpAddress = $userIpAddress;

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

    public function getFullName()
    {
        return $this->getMstSalutation().' '.$this->getFirstName().' '.$this->getLastName();
    }
}
