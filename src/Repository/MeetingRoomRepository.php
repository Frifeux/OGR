<?php

namespace App\Repository;

use App\Entity\MeetingRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method MeetingRoom[]    findAll()
 * @method MeetingRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingRoom::class);
    }

    /**
    * @return MeetingRoom[] Returns an array of active MeetingRoom objects
    */
    public function findActiveMeetingRoom()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return MeetingRoom[] Returns an array of MeetingRoom objects
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
    public function findOneBySomeField($value): ?MeetingRoom
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
