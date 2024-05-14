<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Address cannot be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Address cannot be longer than {{ limit }} characters."
    )]
    private ?string $adresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Phone number cannot be blank.")]
    #[Assert\Regex(
        pattern: "/^[0-9]+$/",
        message: "Phone number should contain only digits."
    )]
    private ?int $num_tel = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Price cannot be blank.")]
    #[Assert\Type(type: "float", message: "Price must be a valid number.")]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'Commande')]
    private ?Livraison $livraison = null;

    #[ORM\ManyToMany(targetEntity: Design::class, mappedBy: 'Commades')]
    private Collection $designs;

    #[ORM\ManyToOne(inversedBy: 'Commandes')]
    private ?Promotion $promotion = null;

    #[ORM\Column(length: 255)]
    private ?string $produits = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    public function __construct()
    {
        $this->designs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->num_tel;
    }

    public function setNumTel(int $num_tel): static
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): static
    {
        $this->livraison = $livraison;

        return $this;
    }

    /**
     * @return Collection<int, Design>
     */
    public function getDesigns(): Collection
    {
        return $this->designs;
    }

    public function addDesign(Design $design): static
    {
        if (!$this->designs->contains($design)) {
            $this->designs->add($design);
            $design->addCommade($this);
        }

        return $this;
    }

    public function removeDesign(Design $design): static
    {
        if ($this->designs->removeElement($design)) {
            $design->removeCommade($this);
        }

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getProduits(): ?string
    {
        return $this->produits;
    }

    public function setProduits(string $produits): static
    {
        $this->produits = $produits;

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
    
}
