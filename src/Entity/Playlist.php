<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * Playlist
 *
 * @ORM\Table(name="playlist")
 * @ORM\Entity
 */
class Playlist
{
    /**
     * @var int
     *
     * @ORM\Column(name="idplaylist", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idplaylist;

    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateplaylist", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateplaylist = 'CURRENT_TIMESTAMP';

    public function getIdplaylist(): ?int
    {
        return $this->idplaylist;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(int $userid): self
    {
        $this->userid = $userid;
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

    public function getDateplaylist(): ?DateTime
    {
        return $this->dateplaylist;
    }

    public function setDateplaylist(?DateTime $dateplaylist): self
    {
        $this->dateplaylist = $dateplaylist;
        return $this;
    }
}
