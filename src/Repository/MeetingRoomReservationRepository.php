<?php

namespace App\Repository;

use App\Entity\MeetingRoomReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingRoomReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingRoomReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingRoomReservation[]    findAll()
 * @method MeetingRoomReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRoomReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingRoomReservation::class);
    }

    // /**
    //  * @return MeetingRoomReservation[] Returns an array of MeetingRoomReservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MeetingRoomReservation
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
