<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Produit
 *
 * @ORM\Table(name="produit", indexes={@ORM\Index(name="categorie_id", columns={"categorie_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="idProduit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idproduit;

    /**
     * @var string
     *
     * @ORM\Column(name="nomProduit", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Veuillez entrer un nom de produit.")
     * @Assert\Length(
     *      min=5,
     *      max=20,
     *      minMessage="Le nom de produit doit comporter {{ limit }} caractères minimum.",
     *      maxMessage="Le nom de produit doit comporter {{ limit }} caractères maximum."
     * )
     */
    private $nomproduit;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer", nullable=false)
     * @Assert\NotBlank(message="Veuillez entrer une quantité.")
     * @Assert\GreaterThan(value=0, message="La quantité doit être supérieure à zéro.")
     */
    private $quantite;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     * @Assert\NotBlank(message="Veuillez entrer le prix.")
     * @Assert\GreaterThan(value=0, message="Le prix doit être supérieur à zéro.")
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="imageProduit", type="string", length=255, nullable=false)
     */
    private $imageproduit;

    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumn(name="categorie_id", referencedColumnName="idCategorie")
     */
    private $categorie;

    public function getIdproduit(): ?int
    {
        return $this->idproduit;
    }

    public function getNomproduit(): ?string
    {
        return $this->nomproduit;
    }

    public function setNomproduit(string $nomproduit): self
    {
        $this->nomproduit = $nomproduit;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImageproduit(): ?string
    {
        return $this->imageproduit;
    }

    public function setImageproduit(string $imageproduit): self
    {
        $this->imageproduit = $imageproduit;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }
}