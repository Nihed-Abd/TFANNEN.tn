<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $avis = null;

    #[ORM\Column(length: 255)]
    private ?string $commentaire = null;

   
    #[ORM\ManyToOne(inversedBy: 'avis', targetEntity: Design::class)]
    private ?Design $design = null;

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvis(): ?float
    {
        return $this->avis;
    }

    public function setAvis(float $avis): static
    {
        $this->avis = $avis;

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

    public function getDesign(): ?Design
    {
        return $this->design;
    }

    public function setDesign(?Design $design): static
    {
        $this->design = $design;

        return $this;
    }

  
}
