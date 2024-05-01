<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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
     * @var string
     *
     * @ORM\Column(name="duree_seance", type="string", length=254, nullable=false)
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
     * @ORM\Column(name="categorie", type="string", length=254, nullable=false)
     */
    private $categorie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=false)
     */
    private $dateFin;

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

    public function getDureeSeance(): ?string
    {
        return $this->dureeSeance;
    }

    public function setDureeSeance(string $dureeSeance): static
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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }


}
