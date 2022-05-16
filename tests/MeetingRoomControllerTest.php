<?php

namespace App\Tests;

use App\Entity\MeetingRoomReservation;
use App\Repository\MeetingRoomReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingRoomControllerTest extends WebTestCase
{
    private $client = null;
    private $testUser = null;

    public function setUp(): void
    {
        $this->client = static::createClient();

        //on récupère l'utilisateur et on le connecte
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneBy(['email' => 'Admin.DOE@mail.com']);
        $this->client->loginUser($this->testUser);
    }

    /**
     * We check if the form shows an error message if the user doesn't fill the form correctly
     */
    public function testAddReservationWithWrongDate(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/meeting-room');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Réserver', [
            'meeting_room_reservation[meetingRoom]' => '1',
            'meeting_room_reservation[title]' => 'new reservation',
            'meeting_room_reservation[description]' => 'new description',
            'meeting_room_reservation[startAt][date]' => '2022-05-05',
            'meeting_room_reservation[startAt][time][hour]' => '10',
            'meeting_room_reservation[startAt][time][minute]' => '0',
            'meeting_room_reservation[endAt][date]' => '2022-05-05',
            'meeting_room_reservation[endAt][time][hour]' => '10',
            'meeting_room_reservation[endAt][time][minute]' => '0',
        ]);

        // on vérifie que la réservation n'a pas été créée et qu'un message d'erreur est affiché
        self::assertSelectorExists('div.invalid-feedback');
    }

    /**
     * We check if the reservation is created correctly
     */
    public function testAddValidReservation(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/meeting-room');
        self::assertResponseIsSuccessful();

        // On ne veut pas le weekend
        $date = new \DateTime('now');
        if (date('w') > 4) {
            $date->modify('+2 day');
        }

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Réserver', [
            'meeting_room_reservation[meetingRoom]' => '1',
            'meeting_room_reservation[title]' => 'new reservation',
            'meeting_room_reservation[description]' => 'new description',
            'meeting_room_reservation[startAt][date]' => $date->format('Y-m-d'),
            'meeting_room_reservation[startAt][time][hour]' => '12',
            'meeting_room_reservation[startAt][time][minute]' => '0',
            'meeting_room_reservation[endAt][date]' => $date->format('Y-m-d'),
            'meeting_room_reservation[endAt][time][hour]' => '12',
            'meeting_room_reservation[endAt][time][minute]' => '30',
        ]);

        self::assertResponseIsSuccessful();

        //on vérifie que la réservation a bien été créée dans la BDD
        $meetingRoomReservation = static::getContainer()->get(MeetingRoomReservationRepository::class);
        $testReservation = $meetingRoomReservation->findOneBy(['title' => 'new reservation']);

        self::assertInstanceOf(MeetingRoomReservation::class, $testReservation);

        //on vérifie que la réservation a bien été créé en vérifiant le message de confirmation
        self::assertSelectorExists('div.alert-success');
        self::assertSelectorTextContains('.alert-success div', 'Votre réservation a bien été ajoutée !');
    }

    /**
     * We check if it returns all meeting rooms reservations for a given meeting room and dates
     */
    public function testGettingReservationForFullcalendar(): void
    {
        $data = [
            'startDate' => '2022-01-01T00:00:00.000Z',
            'endDate' => '2022-12-01T00:00:00.000Z',
        ];

        // récupération des réservations pour la salle de réunion 1 de toute l'année 2022
        $this->client->request('GET', 'meeting-room/reservation/2', $data);
        self::assertResponseIsSuccessful();

        // on vérifie que la réponse est au format JSON
        $response = $this->client->getResponse();
        self::assertJson($response->getContent());

        // on récupère les données de la réponse et on vérifie qu'on a bien une réservation pour la salle de réunion 1
        $jsonData = json_decode($response->getContent(), true);
        self::assertCount(1, $jsonData);

    }
}
