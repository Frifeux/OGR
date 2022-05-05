<?php

namespace App\Tests;

use App\Entity\ResetPasswordRequest;
use App\Repository\MeetingRoomReservationRepository;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * It tests that the login page is accessible, that the form is submitted correctly, and that the user is redirected to
     * the home page, so the user is logged in.
     */
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/login');

        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $client->submitForm('Connexion', [
            '_username' => 'admin.DOE@mail.com',
            '_password' => 'password'
        ]);

        self::assertResponseRedirects('/fr/home');
    }

    /**
     * it shows a message if the user try to log in with an invalid email or password
     */
    public function testWithWrongLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/login');

        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $crawler = $client->submitForm('Connexion', [
            '_username' => 'admin.DOE@mail.com',
            '_password' => 'not_the_password'
        ]);

        $client->followRedirect();

        self::assertSelectorExists('div.alert-danger');
        self::assertSelectorTextContains('div.alert-danger', 'Identifiants invalides.');
    }

    /**
     * We check that the user receive an email when he tries to reset his password.
     */
    public function testForgotPassword(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fr/reset-password');

        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $client->submitForm('Envoyer', [
            'reset_password_request_form[email]' => 'admin.DOE@mail.com'
        ]);

        self::assertResponseRedirects('/fr/reset-password/check-email');

        //on récupère l'utilisateur
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'Admin.DOE@mail.com']);

        //on vérifie que la demande de reinitialisation a bien été créée dans la BDD
        $resetPassword = static::getContainer()->get(ResetPasswordRequestRepository::class);
        $resetPasswordRequest = $resetPassword->findOneBy(['user' => $user]);

        self::assertNotNull($resetPasswordRequest);
    }


}
