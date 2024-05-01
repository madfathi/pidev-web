<?php

namespace App\Entity;
use App\Entity\Client;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProgramRepository;

/**
 * @ORM\Entity(repositoryClass=ProgramRepository::class)
 */
class Program
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="id_p", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idP;

    #[Assert\NotBlank(message:"Le titre ne doit pas etre vide")] 

    #[Assert\Length(max:20,maxMessage:"Le titre ne doit pas contenir plus que 20 caracteres")]
    
    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     */
    private $titre;

    #[Assert\NotBlank(message:"niveau ne doit pas etre vide")] 
    #[Assert\Length(max:20,maxMessage:"Le niveau ne doit pas contenir plus que 20 caracteres")]
    


    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255, nullable=false)
     */
    private $niveau;


    #[Assert\NotBlank(message:"description ne doit pas etre vide")] 
    #[Assert\Length(max:100,maxMessage:"Le niveau ne doit pas contenir plus que 20 caracteres")]
    



    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;




    #[Assert\NotBlank(message:"prix ne doit pas etre vide")] 
    #[Assert\Length(max:20,maxMessage:"Le prix ne doit pas contenir plus que 20 caracteres")]
    


    /**
     * @var int
     *
     * @ORM\Column(name="prix", type="integer", nullable=false)
     */
    private $prix;




    
    




    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=false)
     */
    private $image;














   /**
 * @var \Client
 *
 * @ORM\ManyToOne(targetEntity="Client")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="id_client", referencedColumnName="id_c")
 * })
 */


    private $idClient;
    
     /**
     * @var int
     *
     * @ORM\Column(name="etat", type="integer", length=255, nullable=false, options={"default": 0})
     */
    private $etat = 0;

    public function getIdP(): ?int
    {
        return $this->idP;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getIdClient()
{
    return $this->idClient;
}


    public function setIdClient(?Client $idClient): static
    {
        $this->idClient = $idClient;

        return $this;
    }
    
    public function getEtat(): ?int
    {
        return $this->etat;
    }
    public function setEtat(int $etat): void
    {
        $this->etat = $etat;
    }

}
