<?php

namespace App\Repository;

use App\Entity\Office;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function findActiveOffice(): ?Office
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


    public function searchOffice(string $location = NUll, string $floor = NUll, string $department = NUll): ?Office
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.enabled = :enabled')
            ->setParameter('enabled', true);

        if ($floor !== null) {
            $query->andWhere('m.floor = :floor')
                ->setParameter('floor', $floor);
        }

        if ($department !== null) {
            $query->andWhere('m.department = :department')
                ->setParameter('department', $department);
        }

        if ($location !== null) {
            $query->andWhere('m.location = :location')
                ->setParameter('location', $location);
        }

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
