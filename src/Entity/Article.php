<?php

namespace App\Entity;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Article
 *
 * @ORM\Table(name="article", indexes={@ORM\Index(name="authorID", columns={"authorID"})})
 * @ORM\Entity
 */
class Article
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     * @return AnnotationException
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="please write the title")
     * @Groups("post:read")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="url", type="text", length=0, nullable=false)
     * @Assert\NotBlank(message="please upload pdf")
     * @Assert\File(mimeTypes={"application/pdf"})
     * @Groups("post:read")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255, nullable=false)
     * @Groups("post:read")
     */
    private $content;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Users")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="authorID", referencedColumnName="id")
     * })
     */
    private $authorid;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    public function getAuthorid(): ?Users
    {
        return $this->authorid;
    }

    public function setAuthorid(?Users $authorid): self
    {
        $this->authorid = $authorid;

        return $this;
    }


}
