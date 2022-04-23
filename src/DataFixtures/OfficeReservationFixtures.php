<?php

namespace App\DataFixtures;

use App\Entity\OfficeReservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OfficeReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        // Create 10 office reservations
        for ($i = 0; $i < 10; $i++) {
            $officeReservation = new OfficeReservation();
            $officeReservation->setDescription($faker->words(20, true));

            $officeReservation->setStartAt(new \DateTime('now'));
            $officeReservation->setEndAt(new \DateTime('now +' . $i . ' hour'));

            $officeReservation->setOffice($this->getReference('office_' . $faker->numberBetween(0, 9)));
            $officeReservation->setUser($this->getReference('user_' . $faker->numberBetween(0, 4)));

            $manager->persist($officeReservation);
            $manager->persist($officeReservation);
        }

        $manager->flush();
    }

    // We need to load the OfficeFixtures and UserFixtures before
    public function getDependencies()
    {
        return [
            OfficeFixtures::class,
            UserFixtures::class,
        ];
    }
}
