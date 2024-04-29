<?php

namespace App\Entity;
use App\Entity\Seance;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
   
     /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idReservation;

     /**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank(message="Le type de Reservation ne peut pas être vide.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le type ne peut contenir que des lettres."
 * )
 */
    private $typeReservation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le username ne peut pas être vide.")
     
     */
    private $username;

   /**
 * @ORM\Column(type="string", length=255)
 * @Assert\NotBlank(message="Le mail ne peut pas être vide.")
 * @Assert\Regex(
 *     pattern="/^[^@\s]+@[^@\s]+\.[^@\s]+$/",
 *     message="L'adresse email '{{ value }}' n'est pas valide."
 * )
 */
private $email;

  /**
 * @ORM\Column(type="integer")
 * @Assert\NotBlank(message="Ce champ ne peut pas être vide.")
 * @Assert\Regex(
 *     pattern="/^\d{8}$/",
 *     message="Le nombre doit être composé de 8 chiffres."
 * )
 */
    private $phone;

 /**
     * @var \Seance
     *
     * @ORM\ManyToOne(targetEntity="Seance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_seance", referencedColumnName="id_seance")
     * })
     */
    private $idSeance;
    public function __toString()
    {
        return (string) $this->username;
    }
    public function getId(): ?int
    {
        return $this->idReservation;
    }
    public function getIdSeance(): ?Seance
    {
        return $this->idSeance;
    }

    public function setIdSeance(?Seance $idSeance): static
    {
        $this->idSeance = $idSeance;
        return $this;
    }
    public function getIdReservation(): ?int
    {

        return $this->idReservation;
    
    }

    public function getTypeReservation(): ?string
    {
        return $this->typeReservation;
    }

    public function setTypeReservation(string $typeReservation): static
    {
        $this->typeReservation = $typeReservation;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

   


}
