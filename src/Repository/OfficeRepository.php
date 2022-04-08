<?php

namespace App\Repository;

use App\Entity\Office;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Office|null find($id, $lockMode = null, $lockVersion = null)
 * @method Office|null findOneBy(array $criteria, array $orderBy = null)
 * @method Office[]    findAll()
 * @method Office[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Office::class);
    }

    /**
     * @return Office|null Returns an array of active Office objects
     */
    public function findActiveOffice()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all locations of the active office
     */
    public function getAllLocation(): ?array
    {
        $activeOffice = $this->findActiveOffice();

        // On récupère toutes les localisations
        $locations = [];

        foreach ($activeOffice as $office) {
            $officeLocation = $office->getLocation();
            $locations[$officeLocation] = $officeLocation;
        }

        return $locations;
    }

    /**
     * Get all the floors of the active office
     */
    public function getAllFloor(): ?array
    {
        $activeOffice = $this->findActiveOffice();

        // On récupère tout les étages
        $floors = [];

        foreach ($activeOffice as $office) {
            $officeFloor = $office->getFloor();
            $floors[$officeFloor] = $officeFloor;
        }

        return $floors;
    }

    /**
     * Get all departments of the active office
     */
    public function getAllDepartment(): ?array
    {
        $activeOffice = $this->findActiveOffice();

        // On récupère tout les services
        $departments = [];

        foreach ($activeOffice as $office) {
            $officeDepartments = $office->getDepartment();
            $departments[$officeDepartments] = $officeDepartments;
        }

        return $departments;
    }


    // Sélectionne seulement les bureaux qui n'ont pas de réservation en cours
    //SELECT o.*
    //FROM office o
    //LEFT JOIN office_reservation r
    //ON r.office_id = o.id
    //AND (r.start_at = '2022-04-07 08:00:00' AND r.end_at = '2022-04-07 08:30:00')
    //WHERE r.office_id IS NULL

    public function searchOffice(\DateTime $startAt, \DateTime $endAt, string $location = NUll, string $floor = NUll, string $department = NUll)
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.officeReservations', 'r', Join::WITH, 'r.startAt = :startDate AND r.endAt = :endDate')
            ->Where('o.enabled = :enabled')
            ->setParameter('startDate', $startAt)
            ->setParameter('endDate', $endAt)
            ->setParameter('enabled', true);

        if ($floor !== null) {
            $query->andWhere('o.floor = :floor')
                ->setParameter('floor', $floor);
        }

        if ($department !== null) {
            $query->andWhere('o.department = :department')
                ->setParameter('department', $department);
        }

        if ($location !== null) {
            $query->andWhere('o.location = :location')
                ->setParameter('location', $location);
        }

        $query->andWhere('r.office IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Office[] Returns an array of Office objects
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
    public function findOneBySomeField($value): ?Office
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
