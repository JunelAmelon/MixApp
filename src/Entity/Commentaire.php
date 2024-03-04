<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AudiosProjet;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_projet = null;

    #[ORM\Column(length: 255)]
    private ?string $id_audio = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProjet(): ?int
    {
        return $this->id_projet;
    }

    public function setIdProjet(int $id_projet): static
    {
        $this->id_projet = $id_projet;

        return $this;
    }

    public function getIdAudio(): ?string
    {
        return $this->id_audio;
    }

    public function setIdAudio(string $id_audio): static
    {
        $this->id_audio = $id_audio;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->date_commentaire;
    }

    public function setDateCommentaire(\DateTimeInterface $date_commentaire): static
    {
        $this->date_commentaire = $date_commentaire;

        return $this;
    }

     #[ORM\ManyToOne(targetEntity: AudiosProjet::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "audios_projet_id", referencedColumnName: "id")]
    private ?AudiosProjet $audiosProjet = null;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: "commentaire")]
    private $reponses;
}
