<?php

namespace App\Tests;

use App\Repository\EquipmentRepository;
use App\Repository\EquipmentReservationRepository;
use App\Repository\OfficeRepository;
use App\Repository\OfficeReservationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EquipmentControllerTest extends WebTestCase
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
        $this->client->request('GET', '/fr/equipment');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Voir les disponibilités', [
            'choose_equipment_reservation_form[location]' => '',
            'choose_equipment_reservation_form[type]' => '',
            'choose_equipment_reservation_form[startAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[startAt][time][hour]' => '15',
            'choose_equipment_reservation_form[startAt][time][minute]' => '0',
            'choose_equipment_reservation_form[endAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[endAt][time][hour]' => '15',
            'choose_equipment_reservation_form[endAt][time][minute]' => '0'
        ]);

        // on vérifie que la réservation n'a pas été créée et qu'un message d'erreur est affiché
        self::assertSelectorExists('div.invalid-feedback');
    }

    /**
     * We check if it shows all availabilities with no criteria chosen by the user
     */
    public function testShowAvailabilities(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/equipment');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $crawler = $this->client->submitForm('Voir les disponibilités', [
            'choose_equipment_reservation_form[location]' => '',
            'choose_equipment_reservation_form[type]' => '',
            'choose_equipment_reservation_form[startAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[startAt][time][hour]' => '15',
            'choose_equipment_reservation_form[startAt][time][minute]' => '0',
            'choose_equipment_reservation_form[endAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[endAt][time][hour]' => '15',
            'choose_equipment_reservation_form[endAt][time][minute]' => '30'
        ]);

        self::assertResponseIsSuccessful();

        //on récupère le nombre de bureaux disponibles
        $equipment = static::getContainer()->get(EquipmentRepository::class);
        $equipments = $equipment->search(new \dateTime('2022-05-05 15:00:00'), new \dateTime('2022-05-05 15:30:00'));

        //on vérifie que le nombre de matériels disponibles affiché est égal au nombre de matériels existants dans la base de données
        $table = $crawler->filter('table.js-table');
        $this->assertCount(count($equipments), $table->filter('tbody tr'));
    }

    /**
     * We check if it shows all availabilities with criteria chosen by the user
     */
    public function testShowAvailabilitiesWithCriteria(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/equipment');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $crawler = $this->client->submitForm('Voir les disponibilités', [
            'choose_equipment_reservation_form[location]' => 'Paris',
            'choose_equipment_reservation_form[type]' => '',
            'choose_equipment_reservation_form[startAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[startAt][time][hour]' => '18',
            'choose_equipment_reservation_form[startAt][time][minute]' => '0',
            'choose_equipment_reservation_form[endAt][date]' => '2022-05-05',
            'choose_equipment_reservation_form[endAt][time][hour]' => '18',
            'choose_equipment_reservation_form[endAt][time][minute]' => '30'
        ]);

        self::assertResponseIsSuccessful();

        //on récupère le nombre de matériels disponibles
        $equipment = static::getContainer()->get(EquipmentRepository::class);
        $equipments = $equipment->search(new \dateTime('2022-05-05 15:00:00'), new \dateTime('2022-05-05 15:30:00'), 'Paris');

        //on vérifie que le nombre de matériels disponibles affiché est égal au nombre de matériels existants dans la base de données
        $table = $crawler->filter('table.js-table');
        $this->assertCount(count($equipments), $table->filter('tbody tr'));
    }

    /**
     * We check if the reservation is created correctly
     */
    public function testAddReservation(): void
    {
        $data = [
            'startAt' => '2022-05-05 15:00:00',
            'endAt' => '2022-05-05 15:30:00',
        ];

        // On ajoute une réservation à l'utilisateur pour le matériel 1
        $this->client->request('POST', '/equipment/add/2', $data);

        // on vérifie que la réponse est au format JSON
        $response = $this->client->getResponse()->getContent();
        self::assertJson($response);

        // on vérifie que la réponse est un succès, ça doit renvoyer en json: {"success": "Votre réservation a bien été ajoutée !"}
        $jsonDate = json_decode($response, true);
        self::assertEquals('Votre réservation a bien été ajoutée !', $jsonDate['success']);

        // On vérifie que la réservation a bien été créée en base de données
        $equipmentReservationRepository = static::getContainer()->get(EquipmentReservationRepository::class);
        $office = $equipmentReservationRepository->findOneBy(['equipment' => 2, 'user' => $this->testUser, 'startAt' => new \DateTime('2022-05-05 15:00:00'), 'endAt' => new \DateTime('2022-05-05 15:30:00')]);
        self::assertNotNull($office);
    }
}
