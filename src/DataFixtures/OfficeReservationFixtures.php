<?php

namespace App\DataFixtures;

use App\Entity\OfficeReservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OfficeReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $date = new \DateTime();
        $dayOfWeek = 1;

        // Create 4 office reservations
        for ($i = 0; $i < 4; $i++) {
            $officeReservation = new OfficeReservation();
            $officeReservation->setDescription($faker->words(10, true));

            // We don't want to create a reservation for the weekend
            if ($dayOfWeek > 5) {
                $dayOfWeek = 1;

                // we change the date to the next monday
                $date->modify('+1 week');
            }

            $dateStart = clone $date;
            $dateStart->setISODate($dateStart->format('o'), $dateStart->format('W'), $dayOfWeek);
            $dateStart->setTime($faker->numberBetween($_ENV['WORKING_HOURS_START'], $_ENV['WORKING_HOURS_END'] - 2), 0);
            $officeReservation->setStartAt($dateStart);

            $dateEnd = clone $dateStart;
            $dateEnd->modify('+2 hour');
            $officeReservation->setEndAt($dateEnd);

            $officeReservation->setOffice($this->getReference('office_' . $i));
            $officeReservation->setUser($this->getReference('user_' . $i));

            $dayOfWeek++;

            $manager->persist($officeReservation);
        }

        $manager->flush();
    }

    // We need to load the OfficeFixtures and UserFixtures before
    public function getDependencies(): array
    {
        return [
            OfficeFixtures::class,
            UserFixtures::class,
        ];
    }
}
