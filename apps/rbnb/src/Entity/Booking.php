<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Ad $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format")
     */
    private ?\DateTimeInterface $startDate = null;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="The arrival date must come after the departure date"
     * )
     */
    private ?\DateTimeInterface $endDate = null;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="float")
     */
    private ?float $amount = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $comment = null;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt= new \DateTime();
        }
        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function getDuration(): int
    {
        return $this->endDate->diff($this->startDate)->days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
