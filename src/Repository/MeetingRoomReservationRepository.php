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

    //SELECT * FROM `meeting_room_reservation` m
    //WHERE m.meeting_room_id = 1
    //AND ((m.start_at BETWEEN '2022-04-04 08:00:00' AND '2022-04-04 17:00:00') AND (m.end_at BETWEEN '2022-04-04 08:00:00' AND '2022-04-04 17:00:00')
    //OR (m.start_at <= '2022-04-04 08:00:00' AND m.end_at >= '2022-04-04 17:00:00'))

    public function checkExistingReservation(MeetingRoom $meetingRoom, \DateTime $startDate, \Datetime $endDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.meetingRoom = :meetingRoom AND ((m.startAt BETWEEN :startDate AND :endDate) AND (m.endAt BETWEEN :startDate AND :endDate) OR (m.startAt <= :startDate AND m.endAt >= :endDate))')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('meetingRoom', $meetingRoom)
            ->getQuery()
            ->getResult();
    }

    /**
     * This function get all the reservations for the given meeting room between the given start and end dates
     *
     * @param MeetingRoom $meetingRoom
     * @param DateTime $startDate
     * @param Datetime $endDate
     */
    //SELECT * FROM `meeting_room_reservation` AS m
    // WHERE m.meeting_room_id = 1
    // AND (m.start_at >= '2022-04-27 08:00:00' AND m.end_at <= '2022-04-27 08:30:00')

    public function findReservationsForADateRange(MeetingRoom $meetingRoom, \DateTime $startDate, \Datetime $endDate)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.meetingRoom = :meetingRoom AND (m.startAt >= :startDate AND m.endAt <= :endDate)')
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
