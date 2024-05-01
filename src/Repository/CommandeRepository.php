<?php

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Commande;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, commande::class);
    }

     
    public function findAllSorted()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC') // Remplacez 'champATrier' par le nom du champ selon lequel vous souhaitez trier
            ->getQuery()
            ->getResult();
    }
}
