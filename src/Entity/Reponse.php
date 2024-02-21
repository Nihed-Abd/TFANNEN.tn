<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'reponse', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reclamation $id_reclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le statut ne peut pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le statut ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La décision ne peut pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"La décision ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $decision = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdReclamation(): ?Reclamation
    {
        return $this->id_reclamation;
    }

    public function setIdReclamation(Reclamation $id_reclamation): static
    {
        $this->id_reclamation = $id_reclamation;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDecision(): ?string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): static
    {
        $this->decision = $decision;

        return $this;
    }
}
