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
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $booker = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Ad $ad = null;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format: waiting for yyyy/mm/dd")
     * @Assert\GreaterThan("today", message="Arrival date must be after today..", groups={"front"})
     */
    private ?\DateTimeInterface $startDate = null;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format: waiting for yyyy/mm/dd")
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="The arrival date must come after the departure date"
     * )
     */
    private ?\DateTimeInterface $endDate = null;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format: waiting for yyyy/mm/dd")
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
     * @ORM\PreUpdate
     */
    public function prePersist(): void
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function isBookableDates(): bool
    {
        // Gets the dates where the ad is not available => has already been booked up
        $notAvailableDays = $this->ad->getNotAvailableDays();

        // Gets the booking dates within this interval [startDate, endDate]
        $bookingDays = $this->getDays();

        // Stringify $bookingDays; using arrow function
        $days = array_map(fn($dateTime) => $dateTime->format('Y-m-d'), $bookingDays);

        // Stringify $notAvailableDays
        $notAvailable = array_map(fn($dateTime) => $dateTime->format('Y-m-d'), $notAvailableDays);

        foreach ($days as $day) {
            if (in_array($day, $notAvailable)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array which contains all days between the startDate and endDate
     * of the booking going on
     * The step here in the range function represent a day in seconds (24 * 60 * 60)
     *
     * @return array|\DateTime[]
     */
    public function getDays(): array
    {
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24 * 60 * 60
        );

        return array_map(function ($dateTimestamp) {
            return new \DateTime(date('Y-m-d', $dateTimestamp));
        }, $result);
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
