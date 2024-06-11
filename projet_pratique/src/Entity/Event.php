<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\EventRepository")]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private $title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank]
    private $description;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThan("today", message: "La date doit être dans le futur.")]
    private $date;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private $maxParticipants;

    #[ORM\Column(type: "boolean")]
    private $isPublic;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "events")]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    public function __construct()
    {
        // Attribuer une valeur par défaut à l'id_creator
        $this->creator = 1; // Mettez ici l'ID de l'utilisateur par défaut
    }

    // Getters and Setters

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getCreator(): ?int
    {
        return $this->creator;
    }

    public function setCreator(?int $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

}
