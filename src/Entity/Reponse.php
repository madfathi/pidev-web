<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("reponse")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"idAdmin est obligatoire")]
    #[Groups("reponse")]
    private ?string $idUser = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"note est obligatoire")]
    #[Assert\Length(  
        min : 10,
       minMessage:" Entrer 10 caractères au minimum"
   
        )]
        #[Groups("reponse")]
    private ?string $note = null;

  

    #[ORM\ManyToOne(inversedBy: 'reponses')]
  
    private ?Reclamation $Reclamation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("reponse")]
    private ?\DateTimeInterface $createdAt = null;

    // #[ORM\ManyToOne(inversedBy: 'reponses')]
    // private ?User $userid = null;

    public function getId(): ?int
    {
        return $this->id;
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

   

    public function getReclamation(): ?Reclamation
    {
        return $this->Reclamation;
    }

    public function setReclamation(?Reclamation $Reclamation): self
    {
        $this->Reclamation = $Reclamation;

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

    // public function getUserid(): ?User
    // {
    //     return $this->userid;
    // }

    // public function setUserid(?User $userid): self
    // {
    //     $this->userid = $userid;

    //     return $this;
    // }
}
