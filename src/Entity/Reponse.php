<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse", indexes={@ORM\Index(name="IDX_5FB6DEC72D6BA2D9", columns={"reclamation_id"}), @ORM\Index(name="IDX_5FB6DEC758E0A285", columns={"userid_id"})})
 * @ORM\Entity
 */
class Reponse
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
     * @var int|null
     *
     * @ORM\Column(name="reclamation_id", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $reclamationId = NULL;

    /**
     * @var int|null
     *
     * @ORM\Column(name="userid_id", type="integer", nullable=true, options={"default"="NULL"})
     */
    private $useridId = NULL;

    /**
     * @var string
     *
     * @ORM\Column(name="id_user", type="string", length=255, nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=false)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReclamationId(): ?int
    {
        return $this->reclamationId;
    }

    public function setReclamationId(?int $reclamationId): static
    {
        $this->reclamationId = $reclamationId;

        return $this;
    }

    public function getUseridId(): ?int
    {
        return $this->useridId;
    }

    public function setUseridId(?int $useridId): static
    {
        $this->useridId = $useridId;

        return $this;
    }

    public function getIdUser(): ?string
    {
        return $this->idUser;
    }

    public function setIdUser(string $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


}
