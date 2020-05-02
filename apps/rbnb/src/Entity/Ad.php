<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"title"},
 *     message="This title is already in use. Please choose another one."
 * )
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="10",
     *     max="255",
     *     minMessage="Title must have at least 10 characters",
     *     maxMessage="Title cannot exceed 255 characters"
     * )
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(35, message="Price must be greather than $35")
     */
    private ?float $price = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min="20",
     *     minMessage="introduction must have at least 20 characters"
     * )
     */
    private ?string $introduction = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *     min="100",
     *     minMessage="Content must have at least 100 characters"
     * )
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private ?string $coverImage = null;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message="Number of rooms cannot be less than 1")
     */
    private ?int $rooms = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
     */
    private Collection $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $author = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="ad")
     */
    private Collection $bookings;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTimeInterface", message="Incorrect date format")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private Collection $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdLike", mappedBy="ad", orphanRemoval=true)
     * @var Collection<AdLike>
     */
    private Collection $adLikes;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->adLikes = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            $this->slug = (Slugify::create())->slugify($this->title);
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * Get a comment given by an user (The one who booked up the Ad)
     */
    public function getAuthorComment(User $author): ?Comment
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $author) {
                return $comment;
            }
        }

        return null;
    }

    /**
     * Gets the average of ratings on an Ad
     * @return int
     */
    public function getAverageRatings(): int
    {
        if ($this->comments->count() <= 0) {
            return 0;
        }

        $sum = array_reduce(
            $this->comments->toArray(),
            fn(int $total, Comment $comment) => $total + $comment->getRating(),
            0
        );

        return (int)($sum / $this->comments->count());
    }

    /**
     * Gets all days for which the ad is not available.
     * All booking dates => not available dates
     * It will retrieve all dates between [startDate, endDate] for each ad booking
     * The step here in the range function represent a day in seconds (24 * 60 * 60)
     *
     * @return array|\DateTime[]
     */
    public function getNotAvailableDays(): array
    {
        $notAvailableDays = [];

        foreach ($this->getBookings() as $booking) {
            $result = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24 * 60 * 60
            );

            $days = array_map(
                fn($dateTimestamp) => new \DateTime(date('Y-m-d', $dateTimestamp)),
                $result
            );

            $notAvailableDays = [...$notAvailableDays, ...$days];
        }

        return $notAvailableDays;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

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

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AdLike[]
     */
    public function getAdLikes(): Collection
    {
        return $this->adLikes;
    }

    /**
     * Gets likes of current Ad and checks if the param $user has already liked it
     */
    public function isLikedByUser(User $user): bool
    {
        foreach ($this->getAdLikes() as $adLike) {
            if ($adLike->getUser() === $user) {
                return true;
            }
        }
        return false;
    }

    public function addAdLike(AdLike $adLike): self
    {
        if (!$this->adLikes->contains($adLike)) {
            $this->adLikes[] = $adLike;
            $adLike->setAd($this);
        }

        return $this;
    }

    public function removeAdLike(AdLike $adLike): self
    {
        if ($this->adLikes->contains($adLike)) {
            $this->adLikes->removeElement($adLike);
            // set the owning side to null (unless already changed)
            if ($adLike->getAd() === $this) {
                $adLike->setAd(null);
            }
        }

        return $this;
    }
}
