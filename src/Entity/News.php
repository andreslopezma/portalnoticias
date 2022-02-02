<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\NewsRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class News
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", name="title", length=255)
     * @Assert\NotBlank(message="El campo titulo no puede ser vacio")
     */
    private $title;

    /**
     * @ORM\Column(type="string", name="image", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", name="content", length=65535)
     * @Assert\NotBlank(message="El campo contenido no puede ser vacio")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="news")
     * @Assert\NotBlank(message="El campo categoria no puede ser vacio")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Authors", inversedBy="news")
     * @Assert\NotBlank(message="El campo autor no puede ser vacio")
     */
    private $author;

    /** 
     * @ORM\Column(type="datetime", name="date_creation")
     * @Assert\NotBlank(message="La fecha de creacion no puede ser vacia")
     */
    private $dateCreation;

    /** 
     * @ORM\Column(type="boolean", name="active")
     */
    private $active;


    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->active = true;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?Authors
    {
        return $this->author;
    }

    public function setAuthor(?Authors $author): self
    {
        $this->author = $author;

        return $this;
    }
}
