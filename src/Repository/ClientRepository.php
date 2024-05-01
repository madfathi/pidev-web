<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }


    public function findClientsByNom($nom)
    {
        $queryBuilder = $this->createQueryBuilder('c');
    
        if (!empty($nom)) {
            $queryBuilder
                ->where('LOWER(c.nom) LIKE :nom')
                ->setParameter('nom', '%' . strtolower($nom) . '%');
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
    
    // Uncomment and modify methods as needed
    /*
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySomeField($value): ?Client
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function selectAllClient(): array
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult();
    }
    public function trie()
    {
        return $this->createQueryBuilder('client')
            ->orderBy('client.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function trieDes()
    {
        return $this->createQueryBuilder('client')
            ->orderBy('client.nom', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByNameLike(string $name): array
    {
        return $this->createQueryBuilder('client')
            ->andWhere('client.nom LIKE :name')
            ->setParameter('name', '%' . $name . '%') 
            ->getQuery()
            ->getResult();
    }

}
