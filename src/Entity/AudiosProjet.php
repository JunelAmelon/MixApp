<?php

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
    private ?int $projet_id = null;
     

     #[ORM\Column]
    private ?int $my_id_audio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatAudio(): ?string
    {
        return $this->etat_audio;
    }

     public function getmyIdAudio(): ?string
    {
        return $this->my_id_audio;
    }
    public function setEtatAudio(string $etat_audio): static
    {
        $this->etat_audio = $etat_audio;

        return $this;
    }

    
     public function getProjetId(): ?int
    {
        return $this->projet_id;
    }

  
     public function setProjetId(int $projet_id): static
    {
        $this->projet_id = $projet_id;

        return $this;
    }
    

    
    public function setMyIdAudio(int $my_id_audio): static
    {
        $this->my_id_audio = $my_id_audio;

        return $this;
    }
    // #[ORM\ManyToOne(targetEntity: Audios::class, inversedBy: "audiosProjets")]
    // #[ORM\JoinColumn(name: "my_id_audio", referencedColumnName: "id")]
    private ?Audios $audios = null;
}
