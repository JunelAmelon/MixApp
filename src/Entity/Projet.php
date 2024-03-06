<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomProjet = null;

    #[ORM\Column(type: "date")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $etatProjet = null;

    /**
     * @ORM\OneToMany(targetEntity=AudiosProjet::class, mappedBy="projet")
     */
    private $audiosProjets;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="projets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function __construct()
    {
        $this->audiosProjets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProjet(): ?string
    {
        return $this->nomProjet;
    }

    public function setNomProjet(string $nomProjet): self
    {
        $this->nomProjet = $nomProjet;

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

    public function getEtatProjet(): ?string
    {
        return $this->etatProjet;
    }

    public function setEtatProjet(string $etatProjet): self
    {
        $this->etatProjet = $etatProjet;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|AudiosProjet[]
     */
    public function getAudiosProjets(): Collection
    {
        return $this->audiosProjets;
    }

    public function addAudiosProjet(AudiosProjet $audiosProjet): self
    {
        if (!$this->audiosProjets->contains($audiosProjet)) {
            $this->audiosProjets[] = $audiosProjet;
            $audiosProjet->setProjet($this);
        }

        return $this;
    }

    public function removeAudiosProjet(AudiosProjet $audiosProjet): self
    {
        if ($this->audiosProjets->removeElement($audiosProjet)) {
            // set the owning side to null (unless already changed)
            if ($audiosProjet->getProjet() === $this) {
                $audiosProjet->setProjet(null);
            }
        }

        return $this;
    }
}
