<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="Events")
     * @ORM\JoinColumn(nullable=false)

     */
    private $User;

    /**
     * @Assert\NotBlank(message="Name cannot be empty")
     * @Assert\NotNull(message="Name cannot be Null")
     * @ORM\Column(type="string", length=255)

     */
    private $Name;

    /**
     * @Assert\NotBlank(message="Description cannot be empty")
     * @Assert\NotNull(message="Description cannot be Null")
     * @ORM\Column(type="string", length=255)

     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=255)

     */
    private $Type;

    /**
     * @Assert\NotBlank(message="Location cannot be empty")
     * @Assert\NotNull(message="Location cannot be Null")
     * @ORM\Column(type="string", length=255, nullable=true)

     */
    private $Location;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today",message="Cannot Pick Date < Today's Date")

     */
    private $Date;

    /**
     * @ORM\Column(type="boolean")

     */
    private $Payant;

    /**
     * @Assert\NotBlank(message="Price cannot be empty")
     * @Assert\NotNull(message="Price cannot be Null")
     * @Assert\Positive(message="Price cannot be Negative")
     * @ORM\Column(type="float")

     */
    private $Prix;

    /**
     * @ORM\Column(type="string", length=255)

     */
    private $Status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Image;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="Event",cascade={"remove"})

     */
    private $Reviews;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class)
     * @ORM\JoinTable(name="user_event",
     *      joinColumns={ @ORM\JoinColumn(name="event_id", referencedColumnName="id") },
     *      inverseJoinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id") })


     */
    private $Participants;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Max Participants cannot be empty")
     * @Assert\NotNull(message="Max Participants cannot be Null")
     * @Assert\Positive(message="Max Participants cannot be Negative")

     */
    private $MaxParticipants;

    public function __construct()
    {
        $this->Reviews = new ArrayCollection();
        $this->Participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->User;
    }

    public function setUser(?Users $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(?string $Location): self
    {
        $this->Location = $Location;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getPayant(): ?bool
    {
        return $this->Payant;
    }

    public function setPayant(bool $Payant): self
    {
        $this->Payant = $Payant;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): self
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): ?Collection
    {
        return $this->Reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->Reviews->contains($review)) {
            $this->Reviews[] = $review;
            $review->setEvent($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->Reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getEvent() === $this) {
                $review->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getParticipants(): ?Collection
    {
        return $this->Participants;
    }

    public function addParticipant(Users $participant): self
    {
        if (!$this->Participants->contains($participant)) {
            $this->Participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Users $participant): self
    {
        $this->Participants->removeElement($participant);

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->MaxParticipants;
    }

    public function setMaxParticipants(int $MaxParticipants): self
    {
        $this->MaxParticipants = $MaxParticipants;

        return $this;
    }
}
