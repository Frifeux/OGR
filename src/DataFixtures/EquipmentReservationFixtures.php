<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use App\Entity\EquipmentReservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EquipmentReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $date = new \DateTime();
        $dayOfWeek = 1;

        // Create 4 equipment reservations
        for ($i = 0; $i < 4; $i++) {
            $equipmentReservation = new EquipmentReservation();
            $equipmentReservation->setDescription($faker->words(10, true));

            $equipmentReservation->setEquipment($this->getReference('equipment_' . $i));
            $equipmentReservation->setUser($this->getReference('user_' . $i));

            // We don't want to create a reservation for the weekend
            if ($dayOfWeek > 5) {
                $dayOfWeek = 1;

                // we change the date to the next monday
                $date->modify('+1 week');
            }

            $dateStart = clone $date;
            $dateStart->setISODate($dateStart->format('o'), $dateStart->format('W'), $dayOfWeek);
            $dateStart->setTime($faker->numberBetween($_ENV['WORKING_HOURS_START'], $_ENV['WORKING_HOURS_END'] - 2), 0);
            $equipmentReservation->setStartAt($dateStart);

            $dateEnd = clone $dateStart;
            $dateEnd->modify('+2 hour');
            $equipmentReservation->setEndAt($dateEnd);

            $dayOfWeek++;

            $manager->persist($equipmentReservation);
        }

        $manager->flush();
    }

    // We need to load the equipment fixtures and the user fixtures before
    public function getDependencies(): array
    {
        return [
            EquipmentFixtures::class,
            UserFixtures::class,
        ];
    }
}
