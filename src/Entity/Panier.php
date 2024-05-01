<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Panier
 *
 * @ORM\Table(name="panier")
 * @ORM\Entity
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
     * @var int
     *
     * @ORM\Column(name="nomp", type="integer", nullable=false)
     */
    private $nomp;

    /**
     * @var int
     *
     * @ORM\Column(name="img", type="integer", nullable=false)
     */
    private $img;

    /**
     * @var int
     *
     * @ORM\Column(name="pt", type="integer", nullable=false)
     */
    private $pt;

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

    public function getNomp(): ?int
    {
        return $this->nomp;
    }

    public function setNomp(int $nomp): static
    {
        $this->nomp = $nomp;

        return $this;
    }

    public function getImg(): ?int
    {
        return $this->img;
    }

    public function setImg(int $img): static
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


}
