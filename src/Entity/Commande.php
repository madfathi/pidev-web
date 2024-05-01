<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\ComandeRepository;

/**
 * @ORM\Entity(repositoryClass=ComandeRepository::class)
 */

 class Commande
 {
     /**
      * @var int
      *
      * @ORM\Column(name="idc", type="integer", nullable=false)
      * @ORM\Id
      * @ORM\GeneratedValue(strategy="IDENTITY")
      */
     private $idc;
 
     /**
      * @var int
      * @ORM\Column(name="tel", type="integer", nullable=true)
      * @Assert\Type(type="integer", message="Le numéro de téléphone doit être un nombre.")
      * @Assert\NotBlank(message="Veuillez saisir votre numéro de téléphone.")
      * @Assert\Positive(message="Le champ doit être positif.")
      */
     private $tel;
 
     /**
      * @var string
      * @ORM\Column(name="nom", type="string", length=50, nullable=true)
      * @Assert\NotBlank(message="Veuillez saisir votre nom.")
      */
     private $nom;
 
     /**
      * @var string
      * @ORM\Column(name="pre", type="string", length=50, nullable=true)
      * @Assert\NotBlank(message="Veuillez saisir votre prénom.")
      * @Assert\Length(max=50, maxMessage="Le prénom ne peut pas dépasser {{ limit }} caractères.")
      */
     private $pre;
 
     /**
      * @var string
      * @ORM\Column(name="mail", type="string", length=50, nullable=true)
      * @Assert\NotBlank(message="Veuillez saisir votre adresse email.")
      * @Assert\Email(message="Veuillez saisir une adresse email valide.")
      * @Assert\Length(max=50, maxMessage="L'adresse email ne peut pas dépasser {{ limit }} caractères.")
      */
     private $mail;
 
     /**
      * @var array
      * @ORM\Column(name="pani", type="json", nullable=true)
      */
     private $pani = [];
 
     /**
      * @var string
      * @ORM\Column(name="addr", type="string", length=50, nullable=true)
      * @Assert\NotBlank(message="Veuillez saisir votre adresse.")
      * @Assert\Length(max=50, maxMessage="L'adresse ne peut pas dépasser {{ limit }} caractères.")
      */
     private $addr;
 
     public function getIdc(): ?int
     {
         return $this->idc;
     }
 
     public function getTel(): ?int
     {
         return $this->tel;
     }
 
     public function setTel(int $tel): self
     {
         $this->tel = $tel;
         return $this;
     }
 
     public function getNom(): ?string
     {
         return $this->nom;
     }
 
     public function setNom(string $nom): self
     {
         $this->nom = $nom;
         return $this;
     }
 
     public function getPre(): ?string
     {
         return $this->pre;
     }
 
     public function setPre(string $pre): self
     {
         $this->pre = $pre;
         return $this;
     }
 
     public function getMail(): ?string
     {
         return $this->mail;
     }
 
     public function setMail(string $mail): self
     {
         $this->mail = $mail;
         return $this;
     }
 
     public function getPani(): ?array
     {
         return $this->pani;
     }
 
     public function setPani(array $pani): self
     {
         $this->pani = $pani;
         return $this;
     }
 
     public function getAddr(): ?string
     {
         return $this->addr;
     }
 
     public function setAddr(string $addr): self
     {
         $this->addr = $addr;
         return $this;
     }
     private $jsonData;

    // Getter and setter for $jsonData
    public function getJsonData(): ?array
    {
        return $this->jsonData;
    }

    public function setJsonData(?array $jsonData): self
    {
        $this->jsonData = $jsonData;

        return $this;
    }
 }
