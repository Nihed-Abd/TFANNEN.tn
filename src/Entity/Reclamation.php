<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"L'objet ne peut pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"L'objet ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $objet = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le type de réclamation ne peut pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le type de réclamation ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $type_de_reclamation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"La description de la réclamation ne peut pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"La description de la réclamation ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $description_reclamation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message:"La date ne peut pas être vide.")]
    #[Assert\Type("\DateTimeInterface", message:"La date doit être valide.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToOne(mappedBy: 'id_reclamation', cascade: ['persist', 'remove'])]
    private ?Reponse $reponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): static
    {
        $this->objet = $objet;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTypeDeReclamation(): ?string
    {
        return $this->type_de_reclamation;
    }

    public function setTypeDeReclamation(string $type_de_reclamation): static
    {
        $this->type_de_reclamation = $type_de_reclamation;

        return $this;
    }

    public function getDescriptionReclamation(): ?string
    {
        return $this->description_reclamation;
    }

    public function setDescriptionReclamation(string $description_reclamation): static
    {
        $this->description_reclamation = $description_reclamation;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getReponse(): ?Reponse
    {
        return $this->reponse;
    }

    public function setReponse(Reponse $reponse): static
    {
        // set the owning side of the relation if necessary
        if ($reponse->getIdReclamation() !== $this) {
            $reponse->setIdReclamation($this);
        }

        $this->reponse = $reponse;

        return $this;
    }
    public function __toString(): string
    {
        return $this->objet ?? ''; // Adjust according to your entity's properties
    }
}