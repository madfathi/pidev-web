<?php

namespace App\Entity;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Co;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FideliteRepository;

/**
 * @ORM\Entity(repositoryClass=FideliteRepository::class)
 */
class Fidelite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le code promo ne peut pas être vide.")
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

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCodePromo(): ?string
    {
        return $this->codePromo;
    }

    public function setCodePromo(string $codePromo): self
    {
        $this->codePromo = $codePromo;

        return $this;
    }

}

