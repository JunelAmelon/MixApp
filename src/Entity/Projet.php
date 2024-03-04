<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_projet = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
     private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $etat_projet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProjet(): ?string
    {
        return $this->nom_projet;
    }

    public function setNomProjet(string $nom_projet): static
    {
        $this->nom_projet = $nom_projet;

        return $this;
    }

    public function getDateCreation(): ?string
    {
        return $this->date_creation;
    }

    public function setDateCreation(string $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
    

    public function getEtatProjet(): ?string
    {
        return $this->etat_projet;
    }

    public function setEtatProjet(string $etat_projet): static
    {
        $this->etat_projet = $etat_projet;

        return $this;
    }


}
