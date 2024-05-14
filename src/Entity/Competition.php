<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;  
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom  est obligatoire!')]
    #[Assert\Length(max:30, maxMessage:'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:5, minMessage:'Le nom doit au minimum avoir {{ limit }} caractères.')]
    
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'description  est obligatoire!')]
    #[Assert\Length(max:255, maxMessage:'La description ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:5, minMessage:'La description doit au minimum avoir {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'regle  est obligatoire!')]
    #[Assert\Length(max:255, maxMessage:'La regle ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:5, minMessage:'La regle doit au minimum avoir {{ limit }} caractères.')]
    private ?string $regle = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Recompense est obligatoire!')]
    #[Assert\Length(max:30, maxMessage:'La récompense ne peut pas dépasser {{ limit }} caractères.')]
    #[Assert\Length(min:3, minMessage:'La récompense doit au minimum avoir {{ limit }} caractères.')]
    private ?string $recompense = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire!')]
    #[Assert\GreaterThanOrEqual("today",message: 'La date de début doit être supérieure ou égale à la date d\'aujourd\'hui!')]
    #[Assert\LessThan(propertyPath: 'dateFin', message:'La date de début doit être inférieure à la date de fin!')]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire!')]
    #[Assert\GreaterThan(propertyPath: 'dateDebut',message: 'La date de fin doit être supérieure à la date de début!')]
    private ?\DateTimeInterface $date_fin = null;
    #[ORM\ManyToMany(targetEntity: Design::class, inversedBy: 'competitions')]
    private Collection $Designs;

    public function __construct()
    {
        $this->Designs = new ArrayCollection();
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

    public function getRegle(): ?string
    {
        return $this->regle;
    }

    public function setRegle(string $regle): static
    {
        $this->regle = $regle;

        return $this;
    }

    public function getRecompense(): ?string
    {
        return $this->recompense;
    }

    public function setRecompense(string $recompense): static
    {
        $this->recompense = $recompense;

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
}
