<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(name: "date_début", type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_fin = null;

    #[ORM\Column]
    private ?int $max_participants = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'tournois')]
    private ?Jeu $jeu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->max_participants;
    }

    public function setMaxParticipants(int $max_participants): static
    {
        $this->max_participants = $max_participants;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getJeu(): ?Jeu
    {
        return $this->jeu;
    }

    public function setJeu(?Jeu $jeu): static
    {
        $this->jeu = $jeu;

        return $this;
    }

    /**
     * Compatibility helper for templates: return participants collection or empty array.
     * If you later implement a Participant entity/relation, replace this with the real collection.
     *
     * @return array
     */
    public function getParticipants(): array
    {
        return [];
    }

    /**
     * Compatibility getter for optional registration deadline.
     * Returns null by default; implement a real field if needed.
     */
    public function getDateInscriptionLimite(): ?\DateTime
    {
        return null;
    }
    /**
     * Compatibility getter for optional registration fee.
     */
    public function getFraisInscription(): ?float
    {
        return null;
    }

    /**
     * Compatibility getter for optional prize pool.
     */
    public function getCagnotte(): ?float
    {
        return null;
    }

    /**
     * Compatibility getter for optional format description.
     */
    public function getFormat(): ?string
    {
        return null;
    }

    /**
     * Compatibility getter for optional rules text.
     */
    public function getRegles(): ?string
    {
        return null;
    }

    /**
     * Compatibility getter for optional description.
     */
    public function getDescription(): ?string
    {
        return null;
    }
}