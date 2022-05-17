<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 * @Assert\GroupSequence(groups={"post:read"})
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Reviews")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("post:read")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Event::class, inversedBy="Reviews")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("post:read")
     */
    private $Event;

    /**
     * @Assert\NotBlank(message="Review Cannot Be Null")
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $Review;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->Event;
    }

    public function setEvent(?Event $Event): self
    {
        $this->Event = $Event;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->Review;
    }

    public function setReview(string $Review): self
    {
        $this->Review = $Review;

        return $this;
    }
}
