<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $recompense = null;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getRecompense(): ?string
    {
        return $this->recompense;
    }

    public function setRecompense(string $recompense): static
    {
        $this->recompense = $recompense;

        return $this;
    }

    /**
     * @return Collection<int, Design>
     */
    public function getDesigns(): Collection
    {
        return $this->Designs;
    }

    public function addDesign(Design $design): static
    {
        if (!$this->Designs->contains($design)) {
            $this->Designs->add($design);
        }

        return $this;
    }

    public function removeDesign(Design $design): static
    {
        $this->Designs->removeElement($design);

        return $this;
    }
}
