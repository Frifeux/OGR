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

        // Create 10 equipment reservations
        for ($i = 0; $i < 10; $i++) {
            $equipmentReservation = new EquipmentReservation();
            $equipmentReservation->setDescription($faker->words(20, true));

            $equipmentReservation->setEquipment($this->getReference('equipment_' . $faker->numberBetween(0, 9)));
            $equipmentReservation->setUser($this->getReference('user_' . $faker->numberBetween(0, 4)));

            $equipmentReservation->setStartAt(new \DateTime('now'));
            $equipmentReservation->setEndAt($faker->dateTimeBetween('now', '+' . $i . ' hours'));

            $manager->persist($equipmentReservation);
        }

        $manager->flush();
    }

    // We need to load the equipment fixtures and the user fixtures before
    public function getDependencies()
    {
        return [
            EquipmentFixtures::class,
            UserFixtures::class,
        ];
    }
}
