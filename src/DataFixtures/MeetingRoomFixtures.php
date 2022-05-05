<?php

namespace App\DataFixtures;

use App\Entity\MeetingRoom;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MeetingRoomFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $location = array('Nantes', 'Paris', 'Lyon', 'Marseille');

        // generate 4 meeting rooms
        for ($i = 0; $i < 4; $i++) {
            $meetingRoom = new MeetingRoom();

            $this->addReference('meeting_room_' . $i, $meetingRoom);

            $meetingRoom->setName('Salle de réunion ' . $i);
            $meetingRoom->setLocation($location[$i]);
            $meetingRoom->setCapacity((int)range(1, 20));
            $meetingRoom->setEnabled(true);

            $manager->persist($meetingRoom);
        }

        $manager->flush();
    }
}
