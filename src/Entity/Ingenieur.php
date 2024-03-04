<?php

namespace App\Entity;

use App\Repository\IngenieurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngenieurRepository::class)]
class Ingenieur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_ing = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdIng(): ?int
    {
        return $this->id_ing;
    }

    public function setIdIng(int $id_ing): static
    {
        $this->id_ing = $id_ing;

        return $this;
    }
}
