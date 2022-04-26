<?php

namespace App\Repository;

use App\Entity\Equipment;
use App\Entity\Office;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipment[]    findAll()
 * @method Equipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    /**
     * @return Equipment|null Returns an array of active Equipment objects
     */
    public function findActiveEquipment()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.enabled = :enabled')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all locations of the active Equipment objects
     */
    public function getAllLocation(): ?array
    {
        $activeOffice = $this->findActiveEquipment();

        // On récupère toutes les localisations
        $locations = [];

        foreach ($activeOffice as $equipment) {
            $equipmentLocation = $equipment->getLocation();
            $locations[$equipmentLocation] = $equipmentLocation;
        }

        return $locations;
    }

    /**
     * Get all Types of the active Equipment objects
     */
    public function getAllType(): ?array
    {
        $activeEquipment = $this->findActiveEquipment();

        // On récupère tous les types d'équipements
        $types = [];

        foreach ($activeEquipment as $equipment) {
            $equipmentType = $equipment->getType();
            $types[$equipmentType] = $equipmentType;
        }

        return $types;
    }

    // Sélectionne seulement les bureaux qui n'ont pas de réservation en cours
    //SELECT e.*
    //FROM equipment e
    //LEFT JOIN equipment_reservation r
    //ON r.equipment_id = e.id
    //AND ((r.start_at BETWEEN '2022-04-26 14:00:00' AND '2022-04-26 14:30:00') AND (r.end_at BETWEEN '2022-04-26 14:00:00' AND '2022-04-26 14:30:00') OR (r.start_at <= '2022-04-26 14:00:00' AND r.end_at >= '2022-04-26 14:30:00'))
    //WHERE r.equipment_id IS NULL

    public function search(\DateTime $startAt, \DateTime $endAt, string $location = NUll, string $type = NUll)
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.equipmentReservations', 'r', Join::WITH, '(r.startAt BETWEEN :startDate AND :endDate) AND (r.endAt BETWEEN :startDate AND :endDate) OR (r.startAt <= :startDate AND r.endAt >= :endDate)')
            ->Where('e.enabled = :enabled')
            ->setParameter('startDate', $startAt)
            ->setParameter('endDate', $endAt)
            ->setParameter('enabled', true);

        if ($location !== null) {
            $query->andWhere('e.location = :location')
                ->setParameter('location', $location);
        }

        if ($type !== null) {
            $query->andWhere('e.type = :type')
                ->setParameter('type', $type);
        }

        $query->andWhere('r.equipment IS NULL');

        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Equipment[] Returns an array of Equipment objects
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
    public function findOneBySomeField($value): ?Equipment
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
