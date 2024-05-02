<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="review", indexes={@ORM\Index(name="id_event", columns={"id_event"})})
 * @ORM\Entity
 */
class Review
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_review", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idReview;

    /**
     * @var int
     *
     * @ORM\Column(name="nbr_star", type="integer", nullable=false)
     */
    private $nbrStar;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=40, nullable=false)
     */
    private $description;

    /**
     * @var \Evenment
     *
     * @ORM\ManyToOne(targetEntity="Evenment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_event", referencedColumnName="id_event")
     * })
     */
    private $idEvent;

    public function getIdReview(): ?int
    {
        return $this->idReview;
    }

    public function getNbrStar(): ?int
    {
        return $this->nbrStar;
    }

    public function setNbrStar(int $nbrStar): static
    {
        $this->nbrStar = $nbrStar;

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

    public function getIdEvent(): ?Evenment
    {
        return $this->idEvent;
    }

    public function setIdEvent(?Evenment $idEvent): static
    {
        $this->idEvent = $idEvent;

        return $this;
    }


}
