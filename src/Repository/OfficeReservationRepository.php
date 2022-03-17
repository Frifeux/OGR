<?php

namespace App\Repository;

use App\Entity\OfficeReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OfficeReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficeReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficeReservation[]    findAll()
 * @method OfficeReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficeReservation::class);
    }

    // /**
    //  * @return OfficeReservation[] Returns an array of OfficeReservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OfficeReservation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
