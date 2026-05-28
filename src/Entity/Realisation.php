<?php

namespace App\Entity;

use App\Repository\RealisationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: RealisationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Realisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $position = 0;

    #[ORM\Column(type: 'boolean')]
    private ?bool $homePage = false;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $objectPosition = 'center center';

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 100)]
    private ?string $categorie = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        if (!$this->slug && $this->titre) {
            $slugger = new AsciiSlugger();
            $this->slug = $slugger->slug($this->titre)->lower() . '-' . uniqid(); // transforme "Mon Titre" -> "mon-titre"
        }
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isHomePage(): ?bool
    {
        return $this->homePage;
    }

    public function setHomePage(bool $homePage): static
    {
        $this->homePage = $homePage;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getObjectPosition(): ?string
    {
        return $this->objectPosition;
    }

    public function setObjectPosition(?string $objectPosition): static
    {
        $this->objectPosition = $objectPosition;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
