<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already in use by another user."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="First name is mandatory")
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Last name is mandatory")
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Email is mandatory")
     * @Assert\Email()
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="We need a valid picture url here")
     */
    private ?string $picture = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $hash = null;

    /**
     * @var string|null
     * @Assert\EqualTo(
     *     propertyPath="hash",
     *     message="The two passwords are not the same !"
     * )
     */
    public ?string $passwordConfirm = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Introduction is mandatory")
     * @Assert\Length(
     *     min="20",
     *     minMessage="An introduction must count at least 20 characters",
     *     max="100",
     *     maxMessage="An introduction must not exceed 100 characters",
     * )
     */
    private ?string $introduction = null;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Description is mandatory")
     * @Assert\Length(
     *     min="100",
     *     minMessage="A description must count at least 100 characters"
     * )
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ad", mappedBy="author")
     * @var Collection<Ad>
     */
    private Collection $ads;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     * @var Collection<Role>
     */
    private Collection $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="booker")
     *
     */
    private Collection $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author", orphanRemoval=true)
     * @var Collection<Comment>
     */
    private Collection $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AdLike", mappedBy="user")
     * @var Collection<AdLike>
     */
    private Collection $adLikes;

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->adLikes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            $this->slug = (Slugify::create())->slugify($this->firstName . ' ' . $this->lastName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getRoles(): array
    {
        $roles = $this->userRoles->map(function (Role $role) {
            return $role->getName();
        })->toArray();

        return  ['ROLE_USER', ...$roles];
    }

    public function getPassword(): string
    {
        return $this->hash;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return  $this->userRoles;
    }

    public function addUserRole(Role $role): self
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles[] = $role;
            $role->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $role): self
    {
        if ($this->userRoles->contains($role)) {
            $this->userRoles->removeElement($role);
            $role->removeUser($this);
        }

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
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
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

    public function addAdLike(AdLike $adLike): self
    {
        if (!$this->adLikes->contains($adLike)) {
            $this->adLikes[] = $adLike;
            $adLike->setUser($this);
        }

        return $this;
    }

    public function removeAdLike(AdLike $adLike): self
    {
        if ($this->adLikes->contains($adLike)) {
            $this->adLikes->removeElement($adLike);
            // set the owning side to null (unless already changed)
            if ($adLike->getUser() === $this) {
                $adLike->setUser(null);
            }
        }

        return $this;
    }
}
