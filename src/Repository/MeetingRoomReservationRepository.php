<?php

namespace App\Repository;

use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use DateTime;
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


    /**
     * This function checks if there is an existing reservation for the given meeting room between the given start and end
     * dates
     *
     * @param MeetingRoom $meetingRoom
     * @param DateTime $startDate The start date of the reservation.
     * @param Datetime $endDate The end date of the reservation.
     */
    public function checkExistingReservation(MeetingRoom $meetingRoom, DateTime $startDate, Datetime $endDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.meetingRoom = :meetingRoom AND ((m.startAt BETWEEN :startDate AND :endDate) OR (m.endAt BETWEEN :startDate AND :endDate) OR (m.startAt <= :startDate AND m.endAt >= :endDate))')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('meetingRoom', $meetingRoom)
            ->getQuery()
            ->getResult();
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
