<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_commentaire = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $reponse = null;
      
    #[ORM\Column(type: Types::DATE_MUTABLE)]
     private ?\DateTimeInterface $date_reponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommentaire(): ?int
    {
        return $this->id_commentaire;
    }

    public function setIdCommentaire(int $id_commentaire): static
    {
        $this->id_commentaire = $id_commentaire;

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

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getDateReponse(): ?string
    {
        return $this->date_reponse;
    }

    public function setDateReponse(string $date_reponse): static
    {
        $this->date_reponse = $date_reponse;

        return $this;
    }

     #[ORM\ManyToOne(targetEntity: Commentaire::class, inversedBy: "reponses")]
    #[ORM\JoinColumn(name: "id_commentaire", referencedColumnName: "id")]
    private ?Commentaire $commentaire = null;
}
