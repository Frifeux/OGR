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

        // Create 10 meeting room reservations
        for ($i = 0; $i < 10; $i++) {
            $meetingRoomReservation = new MeetingRoomReservation();
            $meetingRoomReservation->setTitle($faker->words(2, true));
            $meetingRoomReservation->setDescription($faker->words(20, true));

            $meetingRoomReservation->setStartAt(new \DateTime('now'));
            $meetingRoomReservation->setEndAt($faker->dateTimeBetween('now', '+' . $i . ' hours'));

            $meetingRoomReservation->setMeetingRoom($this->getReference('meeting_room_' . $faker->numberBetween(0, 9)));
            $meetingRoomReservation->setUser($this->getReference('user_' . $faker->numberBetween(0, 4)));

            $manager->persist($meetingRoomReservation);
        }

        $manager->flush();
    }

    // We need to load the meetingRoomFixtures and UserFixtures before
    public function getDependencies()
    {
        return [
            MeetingRoomFixtures::class,
            UserFixtures::class,
        ];
    }
}
