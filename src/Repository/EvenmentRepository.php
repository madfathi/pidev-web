<?php
// src/Repository/EvenmentRepository.php

namespace App\Repository;

use App\Entity\Evenment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EvenmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenment::class);
    }



// Custom method to find events by month
public function findEventsByMonth(\DateTimeInterface $date)
{
    $startDate = new \DateTime($date->format('Y-m-01')); // First day of the month
    $endDate = new \DateTime($date->format('Y-m-t')); // Last day of the month

    return $this->createQueryBuilder('e')
        ->andWhere('e.dateEvent BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', $startDate)
        ->setParameter('endDate', $endDate)
        ->getQuery()
        ->getResult();
}

public function findTopEvents($limit = 3): array
{
    return $this->createQueryBuilder('e')
    ->select('e.nomEvent', 'SUM(r.nbrStar) as totalStars')
    ->leftJoin('e.reviews', 'r')
    ->groupBy('e.idEvent')
    ->orderBy('totalStars', 'DESC')
    ->setMaxResults($limit)
    ->getQuery()
    ->getResult();
}
   

    /**
     * Find all events with their associated reviews.
     *
     * @return Evenment[] Returns an array of Evenment objects
     */
    public function findAllWithReviews(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.reviews', 'r')
            ->addSelect('r') // Select the associated reviews
            ->getQuery()
            ->getResult();
    }
}
