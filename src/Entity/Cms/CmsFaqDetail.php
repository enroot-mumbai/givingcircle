<?php

namespace App\Entity\Cms;

use App\Repository\Cms\CmsFaqDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsFaqDetailRepository::class)
 * @ORM\Table("cmsfaqdetail")
 */
class CmsFaqDetail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CmsFaq::class, inversedBy="cmsFaqDetail")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cmsFaq;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $faqQuestion;

    /**
     * @ORM\Column(type="text")
     */
    private $faqAnswer;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sequenceNo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmsFaq(): ?CmsFaq
    {
        return $this->cmsFaq;
    }

    public function setCmsFaq(?CmsFaq $cmsFaq): self
    {
        $this->cmsFaq = $cmsFaq;

        return $this;
    }

    public function getFaqQuestion(): ?string
    {
        return $this->faqQuestion;
    }

    public function setFaqQuestion(string $faqQuestion): self
    {
        $this->faqQuestion = $faqQuestion;

        return $this;
    }

    public function getFaqAnswer(): ?string
    {
        return $this->faqAnswer;
    }

    public function setFaqAnswer(string $faqAnswer): self
    {
        $this->faqAnswer = $faqAnswer;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
