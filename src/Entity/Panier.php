<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\PanierRepository;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @var int
     *
     * @ORM\Column(name="idp", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idp;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer", nullable=false)
     */
    private $quantite;

    /**
     * @var string
     *
     * @ORM\Column(name="nomp", type="string", length=254, nullable=false)
     */
    private $nomp;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=254, nullable=false)
     */
    private $img;

    /**
     * @var int
     *
     * @ORM\Column(name="pt", type="integer", nullable=false)
     */
    private $pt;

    /**
     * @var string
     *
     * @ORM\Column(name="prod_id", type="string", length=100, nullable=false)
     */
    private $prodId;
    

    public function getIdp(): ?int
    {
        return $this->idp;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getNomp(): ?string
    {
        return $this->nomp;
    }

    public function setNomp(string $nomp): static
    {
        $this->nomp = $nomp;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getPt(): ?int
    {
        return $this->pt;
    }

    public function setPt(int $pt): static
    {
        $this->pt = $pt;

        return $this;
    }

    public function getProdId(): ?string
    {
        return $this->prodId;
    }

    public function setProdId(string $prodId): static
    {
        $this->prodId = $prodId;

        return $this;
    }


}
