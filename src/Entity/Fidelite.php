<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fidelite
 *
 * @ORM\Table(name="fidelite")
 * @ORM\Entity
 */
class Fidelite
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="montant", type="integer", nullable=false)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="code_promo", type="string", length=40, nullable=false)
     */
    private $codePromo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCodePromo(): ?string
    {
        return $this->codePromo;
    }

    public function setCodePromo(string $codePromo): static
    {
        $this->codePromo = $codePromo;

        return $this;
    }


}
