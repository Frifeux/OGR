<?php

namespace App\Tests;

use App\Repository\EquipmentRepository;
use App\Repository\EquipmentReservationRepository;
use App\Repository\MeetingRoomReservationRepository;
use App\Repository\OfficeReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationsControllerTest extends WebTestCase
{
    private $client = null;
    private $testUser = null;

    public function setUp(): void
    {
        $this->client = static::createClient();

        //on récupère l'utilisateur et on le connecte
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneBy(['email' => 'John.DOE@mail.com']);
        $this->client->loginUser($this->testUser);
    }

    /**
     * We check that the user can see the list of his reservations
     */
    public function testShowReservations(): void
    {
        //on récupère l'utilisateur et on le connecte
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneBy(['email' => 'John.DOE@mail.com']);
        $this->client->loginUser($this->testUser);

        //on accède à la page de réservation
        $crawler = $this->client->request('GET', '/fr/reservation');
        self::assertResponseIsSuccessful();

        $meetingRoomReservationRepo = static::getContainer()->get(MeetingRoomReservationRepository::class);
        $meetingRoomsReservation = $meetingRoomReservationRepo->findBy(['user' => $this->testUser]);

        //on vérifie le nombre de reservation dans le tableau salle de réunion
        $this->assertEquals(count($meetingRoomsReservation), $crawler->filter('#flush-collapseMeetingroom table > tbody > tr')->count());

        $officeReservationRepo = static::getContainer()->get(OfficeReservationRepository::class);
        $officesReservation = $officeReservationRepo->findBy(['user' => $this->testUser]);

        //on vérifie le nombre de reservation dans le tableau Bureaux
        $this->assertEquals(count($officesReservation), $crawler->filter('#flush-collapseOffice table > tbody > tr')->count());

        $equipmentReservationRepo = static::getContainer()->get(EquipmentReservationRepository::class);
        $equipmentsReservation = $equipmentReservationRepo->findBy(['user' => $this->testUser]);

        //on vérifie le nombre de reservation dans le tableau Matériels
        $this->assertEquals(count($equipmentsReservation), $crawler->filter('#flush-collapseEquipment table > tbody > tr')->count());
    }

    /**
     * We check that the user can delete a meeting room reservation
     */
    public function testDeleteMeetingRoomReservation(): void
    {
        $meetingRoomReservationRepo = static::getContainer()->get(MeetingRoomReservationRepository::class);
        $meetingRoomReservation = $meetingRoomReservationRepo->findOneBy(['user' => $this->testUser]);

        $this->client->request('DELETE', '/fr/meeting-room/delete/' . $meetingRoomReservation->getId());

        // on vérifie que la réponse est au format JSON
        $response = $this->client->getResponse()->getContent();
        self::assertJson($response);

        // on vérifie que la réponse est un succès, ça doit renvoyer en json: {"success": "La salle de réunion à bien été supprimée"}
        $jsonDate = json_decode($response, true);
        self::assertEquals('La salle de réunion à bien été supprimée', $jsonDate['success']);

        $meetingRoomReservationAfterDelete = $meetingRoomReservationRepo->findOneBy([
            'user' => $this->testUser,
            'meetingRoom' => $meetingRoomReservation->getId()
        ]);

        // on vérifie que la réservation n'existe plus
        self::assertNull($meetingRoomReservationAfterDelete);
    }

    /**
     * We check that the user can delete an office reservation
     */
    public function testDeleteOfficeRoomReservation(): void
    {
        $officeReservationRepo = static::getContainer()->get(OfficeReservationRepository::class);
        $officeReservation = $officeReservationRepo->findOneBy(['user' => $this->testUser]);

        $this->client->request('DELETE', '/fr/office/delete/' . $officeReservation->getId());

        // on vérifie que la réponse est au format JSON
        $response = $this->client->getResponse()->getContent();
        self::assertJson($response);

        // on vérifie que la réponse est un succès, ça doit renvoyer en json: {"success": "Le bureau à bien été supprimé"}
        $jsonDate = json_decode($response, true);
        self::assertEquals('Le bureau à bien été supprimé', $jsonDate['success']);

        $officeReservationAfterDelete = $officeReservationRepo->findOneBy([
            'user' => $this->testUser,
            'office' => $officeReservation->getId()
        ]);

        // on vérifie que la réservation n'existe plus
        self::assertNull($officeReservationAfterDelete);
    }

    /**
     * We check that the user can delete equipment reservation
     */
    public function testDeleteEquipmentRoomReservation(): void
    {
        $equipmentReservationRepo = static::getContainer()->get(EquipmentReservationRepository::class);
        $equipmentReservation = $equipmentReservationRepo->findOneBy(['user' => $this->testUser]);

        $this->client->request('DELETE', '/fr/equipment/delete/' . $equipmentReservation->getId());

        // on vérifie que la réponse est au format JSON
        $response = $this->client->getResponse()->getContent();
        self::assertJson($response);

        // on vérifie que la réponse est un succès, ça doit renvoyer en json: {"success": "Le matériel à bien été supprimé"}
        $jsonDate = json_decode($response, true);
        self::assertEquals('Le matériel à bien été supprimé', $jsonDate['success']);

        $equipmentReservationAfterDelete = $equipmentReservationRepo->findOneBy([
            'user' => $this->testUser,
            'equipment' => $equipmentReservation->getId()
        ]);

        // on vérifie que la réservation n'existe plus
        self::assertNull($equipmentReservationAfterDelete);
    }


}