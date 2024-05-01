<?php

namespace App\Entity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SeanceRepository;

/**
 * @ORM\Entity(repositoryClass=SeanceRepository::class)
 */
class Seance

{
    
  /**
 * @ORM\Id
 * @ORM\GeneratedValue
 * @ORM\Column(type="integer")
 */
private $idSeance;

     /**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank(message="Le type de séance ne peut pas être vide.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le type de séance ne peut contenir que des lettres."
 * )
 */
    private  $typeSeance ;

     /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La duree de Seance ne peut pas être vide.")
     
     */
    private  $dureeSeance;

    /**
     * @ORM\Column(type="integer")
     * * @Assert\Range(
 *      min = 1,
 *      max = 10,
 *      notInRangeMessage = "Le nombre doit être compris entre {{ min }} et {{ max }}.",
 * )
 *  @Assert\NotBlank(message="Le champ nb_maximal ne peut pas être vide.")
     
     */
    private  $nbMaximal = NULL;

     /**
 * @ORM\Column(type="string", length=255)
 
 */  
    private  $categorie ;

  /**
 * @var \DateTime|null
 * @ORM\Column(type="date", nullable=true)
 * @Assert\NotNull(message="Le champ ne peut pas être vide.")
 
 */
private $dateFin;

public function validateDateFin($value, ExecutionContextInterface $context)
{
    if ($value !== null && $value < new \DateTime()) {
        $context->buildViolation('La date de fin ne peut pas être antérieure à la date actuelle.')
            ->atPath('dateFin')
            ->addViolation();
    }
}
    public function __toString()
    {
        return (string) $this->typeSeance;
    }
    public function getId(): ?int
    {
        return $this->idSeance;
    }
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
        return $this->dateFin instanceof \DateTimeInterface ? $this->dateFin : null;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }


}
