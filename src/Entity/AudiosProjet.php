<?php

// src/Entity/AudiosProjet.php

namespace App\Entity;

use App\Repository\AudiosProjetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AudiosProjetRepository::class)]
class AudiosProjet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etat_audio = null;

    #[ORM\Column]
    private ?int $id_projet = null;

    #[ORM\Column]
    private ?int $id_audio = null;

    #[ORM\ManyToOne(targetEntity: Projet::class, inversedBy: "audiosProjets")]
    #[ORM\JoinColumn(name: "id_projet", referencedColumnName: "id")]
    private ?Projet $projet = null;

    #[ORM\ManyToOne(targetEntity: Audios::class, inversedBy: "audiosProjets")]
    #[ORM\JoinColumn(name: "id_audio", referencedColumnName: "id")]
    private ?Audios $audios = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatAudio(): ?string
    {
        return $this->etat_audio;
    }

    public function setEtatAudio(string $etat_audio): self
    {
        $this->etat_audio = $etat_audio;

        return $this;
    }

    public function getIdProjet(): ?int
    {
        return $this->id_projet;
    }

    public function setIdProjet(int $id_projet): self
    {
        $this->id_projet = $id_projet;

        return $this;
    }

    public function getIdAudio(): ?int
    {
        return $this->id_audio;
    }

    public function setIdAudio(int $id_audio): self
    {
        $this->id_audio = $id_audio;

        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getAudios(): ?Audios
    {
        return $this->audios;
    }

    public function setAudios(?Audios $audios): self
    {
        $this->audios = $audios;

        return $this;
    }
}
