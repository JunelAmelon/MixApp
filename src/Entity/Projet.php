<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime; // Importe la classe DateTime
use DateTimeInterface; // Importe l'interface DateTimeInterface


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
    private ?DateTimeInterface $date_creation = null;


    #[ORM\Column(length : 255)]
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

    public function getDateCreation(): ?DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(DateTimeInterface $date_creation): static
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

    #[ORM\OneToMany(targetEntity: AudiosProjet::class, mappedBy: "projet")]
    private $audiosProjets;

    #[ORM\Column]
    private ?string $id_client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    public function getIdClient(): ?string
    {
        return $this->id_client;
    }

    public function setIdClient(string $id_client): static
    {
        $this->id_client = $id_client;

        return $this;
    }

      public function SetUserIdentifier(?string $id_client): static
    {
       $this->id_client = $id_client;

     return $this;
            
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

//   #[ORM\OneToOne(targetEntity: Client::class, mappedBy: "projet")]
//     private $id_client;

}
