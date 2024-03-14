<?php

namespace App\Entity;

use App\Repository\AudiosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
 
 

#[ORM\Entity(repositoryClass: AudiosRepository::class)]
class Audios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $files = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dates_ajout = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiles(): ?string
    {
        return $this->files;
    }

    public function setFiles(string $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function getDatesAjout(): ?\DateTimeInterface
    {
        return $this->dates_ajout;
    }

    public function setDatesAjout(\DateTimeInterface $dates_ajout): static
    {
        $this->dates_ajout = $dates_ajout;

        return $this;
    }
     #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "audios")]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Ingenieur::class, inversedBy: "audios")]
    #[ORM\JoinColumn(name: "ingenieur_id", referencedColumnName: "id")]
    private ?Ingenieur $ingenieur = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: "audio")]
    private $commentaires;

























































































































































































































































































    
     

}
