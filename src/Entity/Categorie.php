<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idcateg;

    /**
     * @Assert\NotBlank(message="remplir le champs ")
     * @ORM\Column(type="string", length=255)
     */
    private $namecateg;

    /**
     * @Assert\NotBlank(message="remplir le champs titre")
     * @ORM\Column(type="string", length=255)
     */
    private $descriptioncateg;

    /**

     * @ORM\OneToMany(targetEntity=Podcasts::class, mappedBy="categorie")
     * @ORM\JoinColumn(name="Podcasts", referencedColumnName="id")
     */
    private $podcasts;

    public function __construct()
    {
        $this->podcasts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->idcateg;
    }

    public function getNamecateg(): ?string
    {
        return $this->namecateg;
    }

    public function setNamecateg(string $namecateg): self
    {
        $this->namecateg = $namecateg;

        return $this;
    }

    public function getDescriptioncateg(): ?string
    {
        return $this->descriptioncateg;
    }

    public function setDescriptioncateg(string $descriptioncateg): self
    {
        $this->descriptioncateg = $descriptioncateg;

        return $this;
    }

    /**
     * @return Collection<int, Podcasts>
     */
    public function getPodcasts(): Collection
    {
        return $this->podcasts;
    }

    public function addPodcast(Podcasts $podcast): self
    {
        if (!$this->podcasts->contains($podcast)) {
            $this->podcasts[] = $podcast;
            $podcast->setCategorie($this);
        }

        return $this;
    }

    public function removePodcast(Podcasts $podcast): self
    {
        if ($this->podcasts->removeElement($podcast)) {
            // set the owning side to null (unless already changed)
            if ($podcast->getCategorie() === $this) {
                $podcast->setCategorie(null);
            }
        }

        return $this;
    }
}
