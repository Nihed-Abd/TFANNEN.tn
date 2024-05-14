<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; 
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\Entity(repositoryClass: PromotionRepository::class)]
class Promotion
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'le nom  est obligatoire!')]
    #[Assert\Length(max:30, maxMessage:'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:5, minMessage:'Le nom doit au minimum avoir {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'description  est obligatoire!')]
    #[Assert\Length(max:255, maxMessage:'La description ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:5, minMessage:'La description doit au minimum avoir {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'taux_reduction est obligatoire!')]
    #[Assert\Positive(message:'La récompense doit etre positive.')]
    private ?float $taux_reduction = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex( pattern:"/^#/",message:"Code promo should start with '#'")]
    private ?string $code_promo = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire!')]
    #[Assert\GreaterThanOrEqual("today",message: 'La date de début doit être supérieure ou égale à la date d\'aujourd\'hui!')]
    #[Assert\LessThan(propertyPath: 'dateFin', message:'La date de début doit être inférieure à la date de fin!')]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire!')]
    #[Assert\GreaterThan(propertyPath: 'dateDebut',message: 'La date de fin doit être supérieure à la date de début!')]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'type_promo  est obligatoire!')]
    private ?string $type_promo = null;
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'promotion')]
    private Collection $Commandes;

    public function __construct()
    {
        $this->Commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTauxReduction(): ?float
    {
        return $this->taux_reduction;
    }

    public function setTauxReduction(float $taux_reduction): static
    {
        $this->taux_reduction = $taux_reduction;

        return $this;
    }

    public function getCodePromo(): ?string
    {
        return $this->code_promo;
    }

    public function setCodePromo(string $code_promo): static
    {
        $this->code_promo = $code_promo;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getTypePromo(): ?string
    {
        return $this->type_promo;
    }

    public function setTypePromo(string $type_promo): static
    {
        $this->type_promo = $type_promo;

        return $this;
    }
}
