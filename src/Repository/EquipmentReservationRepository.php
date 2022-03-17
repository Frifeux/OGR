<?php

namespace App\Repository;

use App\Entity\EquipmentReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipmentReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentReservation[]    findAll()
 * @method EquipmentReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentReservation::class);
    }

    // /**
    //  * @return EquipmentReservation[] Returns an array of EquipmentReservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipmentReservation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
