<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seance
 *
 * @ORM\Table(name="seance")
 * @ORM\Entity
 */
class Seance
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_seance", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSeance;

    /**
     * @var string
     *
     * @ORM\Column(name="type_seance", type="string", length=40, nullable=false)
     */
    private $typeSeance;

    /**
     * @var int
     *
     * @ORM\Column(name="duree_seance", type="integer", nullable=false)
     */
    private $dureeSeance;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_maximal", type="integer", nullable=false)
     */
    private $nbMaximal;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=200, nullable=false)
     */
    private $categorie;

    public function getIdSeance(): ?int
    {
        return $this->idSeance;
    }

    public function getTypeSeance(): ?string
    {
        return $this->typeSeance;
    }

    public function setTypeSeance(string $typeSeance): static
    {
        $this->typeSeance = $typeSeance;

        return $this;
    }

    public function getDureeSeance(): ?int
    {
        return $this->dureeSeance;
    }

    public function setDureeSeance(int $dureeSeance): static
    {
        $this->dureeSeance = $dureeSeance;

        return $this;
    }

    public function getNbMaximal(): ?int
    {
        return $this->nbMaximal;
    }

    public function setNbMaximal(int $nbMaximal): static
    {
        $this->nbMaximal = $nbMaximal;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }


}
