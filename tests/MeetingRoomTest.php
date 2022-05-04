<?php

//namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingRoomTest extends WebTestCase
{
    public function testAddReservation(): void
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['email' => 'Admin.DOE@mail.com']);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/fr/meeting-room');
        self::assertResponseIsSuccessful();

        $client->submitForm('Réserver',[
            'meeting_room_reservation[meetingRoom]' => '1',
            'meeting_room_reservation[title]' => 'new reservation',
            'meeting_room_reservation[description]' => 'new description',
            'meeting_room_reservation[startAt][date]' => '2022-01-01',
            'meeting_room_reservation[startAt][time][hour]' => '10',
            'meeting_room_reservation[startAt][time][minute]' => '00',
            'meeting_room_reservation[endAt][date]' => '2022-01-01',
            'meeting_room_reservation[endAt][time][hour]' => '11',
            'meeting_room_reservation[endAt][time][minute]' => '00',
        ], 'POST');

        self::assertResponseIsSuccessful();

        // On verifie que le post du formulaire est bien passé
        self::assertRouteSame('app_meeting_room');

//        $client->followRedirect();
//        self::assertResponseRedirects('/fr/meeting-room');
//
        self::assertSelectorExists('div.alert-success');
        self::assertSelectorTextContains('.alert-success div', 'Votre réservation a bien été ajoutée !');
    }
}
