<?php

namespace App\Entity;


use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Annotation\Groups;




#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("reclamation")]
    private ?int $id = null;

   
    #[ORM\GeneratedValue(strategy:"AUTO")]
    #[ORM\Column(length: 255, unique :true)]
    #[Assert\Regex(
            pattern:"/^[a-zA-Z0-9]+$/"
        )]
        #[Groups("reclamation")]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"nom est obligatoire")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: 'Le nom ne doit contenir que des lettres.'
    )]
    #[Groups("reclamation")]
    private ?string $nomD = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"prenom est obligatoire")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: 'Le prenom ne doit contenir que des lettres.'
    )]
    #[Groups("reclamation")]
    private ?string $prenomD = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"cin est obligatoire")]
    #[Assert\Regex(
        pattern: '/^[0-9]{8}$/',
        message: 'CIN doit contenir exactement 8 chiffres sans caractères.'
    )]
    #[Groups("reclamation")]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"email est obligatoire")]
    #[Assert\Email(message:"email n'est pas valide")]

    #[Groups("reclamation")]
    private ?string $email = null;

    

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message:"commentaire est obligatoire")]
    #[Assert\Length(  
         min : 10,
        minMessage:" Entrer 10 caractères au minimum"
    
         )]
         #[Groups("reclamation")]
    private ?string $commentaire = null;
  

    #[ORM\Column]
    #[Groups("reclamation")]
    private ?\DateTime $createdAt ;

   

    #[ORM\Column(length: 255)]
    #[Groups("reclamation")]
    private ?string $statut = "En cours";

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("reclamation")]
    private ?string $file = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"tel est obligatoire")]
    #[Groups("reclamation")]
    private ?string $tel = null;

    #[ORM\OneToMany(mappedBy: 'Reclamation', targetEntity: Reponse::class)]
    private Collection $reponses;

    // #[ORM\ManyToOne(inversedBy: 'reclamations')]
    // private ?User $userid = null;


    

  

  



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getNomD(): ?string
    {
        return $this->nomD;
    }

    public function setNomD(string $nomD): self
    {
        $this->nomD = $nomD;

        return $this;
    }

    public function getPrenomD(): ?string
    {
        return $this->prenomD;
    }

    public function setPrenomD(string $prenomD): self
    {
        $this->prenomD = $prenomD;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

 
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    
    

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function __construct()
    {
        $this->reference = uniqid();
        $this->reference = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $this->reponses = new ArrayCollection();
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setReclamation($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getReclamation() === $this) {
                $reponse->setReclamation(null);
            }
        }

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
