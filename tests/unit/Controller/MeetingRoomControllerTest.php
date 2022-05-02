<?php

namespace unit\Controller;

use App\Controller\MeetingRoomController;
use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use App\Repository\MeetingRoomRepository;
use App\Repository\MeetingRoomReservationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MeetingRoomControllerTest extends TestCase
{

    public function testGetReservationForFullCalendar(): void
    {


        $meetingRoom = new MeetingRoom();
        $meetingRoom->setName("Salle de réunion 0");

        $meetingRoomRepository = $this->createMock(MeetingRoomRepository::class);
        $meetingRoomRepository->expects($this->any())
            ->method('find')
            ->willReturn($meetingRoom);

        $meetingRoomReservation = new MeetingRoomReservation();
        $meetingRoomReservation->setMeetingRoom($meetingRoom);

        $meetingRoomReservationRepository = $this->createMock(MeetingRoomReservationRepository::class);
        $meetingRoomReservationRepository->expects($this->any())
            ->method('find')
            ->willReturn($meetingRoomReservation);

        $objectManager = $this->createMock(ObjectManager::class);

        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($meetingRoomRepository);

        $meetingRoomController = new MeetingRoomController($objectManager);

    }
}
