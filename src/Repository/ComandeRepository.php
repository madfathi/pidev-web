<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

   public function findByCommandeByNom($nom)
{
    return $this->createQueryBuilder('c') // 'c' is an alias for "Commande"
        ->where('c.nom LIKE :nom')
        ->setParameter('nom', '%' . $nom . '%')
        ->getQuery()
        ->getResult();
}
public function findBySortedField(string $fieldName): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.' . $fieldName, 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function orderByNomASC()
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.nom', 'ASC')
            ->getQuery()->getResult();
    }
    public function countCommandsByAddress(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery('
            SELECT c.addr AS adresse, COUNT(c.idc) AS total
            FROM App\Entity\Commande c
            GROUP BY c.adresse
        ');

        return $query->getResult();
    }

//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
