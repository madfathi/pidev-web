<?php

namespace App\Repository;

use App\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 *
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

//    /**
//     * @return Program[] Returns an array of Program objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Program
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findprogramBytitre($titre)
{
    $queryBuilder = $this->createQueryBuilder('c');

    if (!empty($titre)) {
        // Perform a case-insensitive search by converting both the search query and the database field to lowercase
        $queryBuilder->where('LOWER(c.titre) LIKE :titre')
                     ->setParameter('titre', '%' . strtolower($titre) . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}
public function findByTitre(string $searchTerm): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.titre LIKE :searchTerm')
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->getQuery()
        ->getResult();
}
public function findAll(): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.etat = :etat')
        ->setParameter('etat', 0)
        ->getQuery()
        ->getResult();
}


public function findProgramsByClientId(string $clientId): array
{
    return $this->createQueryBuilder('p')
        ->join('p.idClient', 'c')
        ->andWhere('c.idC = :clientId')
        ->setParameter('clientId', $clientId)
        ->getQuery()
        ->getResult();
}
}
