<?php

namespace App\Repository;

use App\Entity\Fidelite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fidelite>
 *
 * @method Fidelite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fidelite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fidelite[]    findAll()
 * @method Fidelite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FideliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fidelite::class);
    }

//    /**
//     * @return Fidelite[] Returns an array of Fidelite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Fidelite
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
