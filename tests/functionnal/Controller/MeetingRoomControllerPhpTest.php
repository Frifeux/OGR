<?php

namespace App\Tests\Functionnal\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingRoomControllerPhpTest extends WebTestCase
{
    public function testMeetingRoomRoute(): void
    {
        $client = static::createClient();
        $client->request('GET', '/meeting-room');

        self::assertResponseIsSuccessful("The response is successful when the route meeting-room is called");
        self::assertSelectorTextContains('p', 'Salle');
    }

    // Test si le formulaire est bien présent
    public function testMeetingRoomForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/meeting-room');

        self::assertSelectorExists('form[name="meeting_room_reservation"]', "The form is present");
        self::assertSelectorExists('button[type="submit"]', "The button text is present");
    }

    // Test si la réservation d'une salle fonctionne
    public function testMeetingRoomReservation(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/meeting-room');

        $form = $client->getCrawler()->selectButton('Réserver')->form();
        $form['meeting_room_reservation[meetingRoom]']->select('Salle de réunion 0');
        $form['meeting_room_reservation[title]']->setValue('Test');
        $form['meeting_room_reservation[description]']->setValue('test');

        $form['meeting_room_reservation[startAt][date]']->setValue('2020-01-01');
        $form['meeting_room_reservation[startAt][time]']->setValue('10:00');

        $form['meeting_room_reservation[endAt][date]']->setValue('2020-01-01');
        $form['meeting_room_reservation[endAt][time]']->setValue('11:00');

//        $client->submitForm('meeting_room_reservation[save]', [
//
//            // TODO: L'id de la salle est à changer car il peut changer dans la BDD
//            'meeting_room_reservation[meetingRoom]' => '101',
//
//            'meeting_room_reservation[title]' => 'Réunion de test',
//            'meeting_room_reservation[description]' => 'Description Réunion de test',
//
//            'meeting_room_reservation[startAt][date]' => '2022-04-28',
//            'meeting_room_reservation[startAt][time][hour]' => '10',
//            'meeting_room_reservation[startAt][time][minute]' => '00',
//
//            'meeting_room_reservation[endAt][date]' => '2022-04-28',
//            'meeting_room_reservation[endAt][time][hour]' => '12',
//            'meeting_room_reservation[endAt][time][minute]' => '00',
//
//        ]);

        self::assertResponseRedirects('/meeting-room', 302, "The response redirects to the route meeting-room");


    }
}
