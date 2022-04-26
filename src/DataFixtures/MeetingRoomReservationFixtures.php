<?php

namespace App\DataFixtures;

use App\Entity\MeetingRoomReservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MeetingRoomReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $date = new \DateTime();
        $dayOfWeek = 1;

        // Create 10 meeting room reservations
        for ($i = 0; $i < 10; $i++) {

            $meetingRoomReservation = new MeetingRoomReservation();
            $meetingRoomReservation->setTitle($faker->words(2, true));
            $meetingRoomReservation->setDescription($faker->words(20, true));

            // We don't want to create a reservation for the weekend
            if ($dayOfWeek > 5) {
                $dayOfWeek = 1;

                // we change the date to the next monday
                $date->modify('+1 week');
            }

            $dateStart = clone $date;
            $dateStart->setISODate($dateStart->format('o'), $dateStart->format('W'), $dayOfWeek);
            $dateStart->setTime($faker->numberBetween($_ENV['WORKING_HOURS_START'], $_ENV['WORKING_HOURS_END'] - 2), 0);
            $meetingRoomReservation->setStartAt($dateStart);

            $dateEnd = clone $dateStart;
            $dateEnd->modify('+2 hour');
            $meetingRoomReservation->setEndAt($dateEnd);

            $meetingRoomReservation->setMeetingRoom($this->getReference('meeting_room_' . $i));
            $meetingRoomReservation->setUser($this->getReference('user_' . $i));

            $dayOfWeek++;

            $manager->persist($meetingRoomReservation);
        }

        $manager->flush();
    }

    // We need to load the meetingRoomFixtures and UserFixtures before
    public function getDependencies(): array
    {
        return [
            MeetingRoomFixtures::class,
            UserFixtures::class,
        ];
    }
}
