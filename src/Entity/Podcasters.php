<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users;

/**
 * Podcasters
 *
 * @ORM\Table(name="Podcasters")
 * @ORM\Entity
 */
class Podcasters extends Users
{
    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=40, nullable=true, options={"comment"="This attribute can be used as the name of the podcast and not necessarily that of the account holder."})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=40, nullable=true, options={"comment"="This attribute can be used as the name of the podcast and not necessarily that of the account holder."})
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="biography", type="string", length=200, nullable=true, options={"comment"="A short and sweet paragraph that tells users a little bit about the podcaster."})
     */
    private $biography;

    public function getRoles()
    {
        return array('ROLE_PODCASTERS');
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;
        return $this;
    }

}

