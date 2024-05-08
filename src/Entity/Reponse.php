<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ReponseRepository;

/**
 * Reponse
 *
 * @ORM\Entity(repositoryClass=ReponseRepository::class)
 */
class Reponse
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column
     * @Groups("reponse")
     */
    private  $id = null;

    /**
     * @var string|null
     *
     * @ORM\Column(length=255)
     * @Assert\NotBlank(message="ID utilisateur est obligatoire")
     * @Groups("reponse")
     */
    private  $idUser = null;

    /**
     * @var string|null
     *
     * @ORM\Column(length=255)
     * @Assert\NotBlank(message="Note est obligatoire")
     * @Assert\Length(
     *      min=10,
     *      minMessage="Entrer 10 caractères au minimum"
     * )
     * @Groups("reponse")
     */
    private $note = null;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type=Types::DATETIME_MUTABLE)
     * @Groups("reponse")
     */
    private$createdAt = null;

    /**
     * @var Reclamation|null
     *
     * @ORM\ManyToOne(targetEntity="Reclamation", inversedBy="reponses")
     */
    private  $Reclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getIdUser(): ?string
    {
        return $this->idUser;
    }

    public function setIdUser(string $idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->Reclamation;
    }

    public function setReclamation(?Reclamation $Reclamation): self
    {
        $this->Reclamation = $Reclamation;
        return $this;
    }
}