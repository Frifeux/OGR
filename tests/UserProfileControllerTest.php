<?php

namespace App\Tests;

use App\Repository\EquipmentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserProfileControllerTest extends WebTestCase
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
     * We test the password reset but not with the same two passwords
     */
    public function testResetPasswordFormWithNotSamePassword(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/user/profile');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Changer le mot de passe', [
            'change_password_form[plainPassword][first]' => 'SfSm&@8T',
            'change_password_form[plainPassword][second]' => 'Jt#K6MS?',
        ]);

        // on vérifie que le changement de mot de passe n'a pas été effectué et qu'un message d'erreur est affiché
        self::assertSelectorExists('div.invalid-feedback');
    }

    /**
     * We test the password reset with the same two passwords and match the needed pattern
     */
    public function testResetPasswordForm(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/user/profile');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Changer le mot de passe', [
            'change_password_form[plainPassword][first]' => 'SfSm&@8T',
            'change_password_form[plainPassword][second]' => 'SfSm&@8T',
        ]);

        self::assertResponseIsSuccessful();

        //on récupère l'utilisateur en BDD
        $userRepo = static::getContainer()->get(UserRepository::class);
        $user = $userRepo->findOneBy(['enabled' => true, 'email' => 'John.DOE@mail.com']);

        // on vérifie le hash du mot de passe
        self::assertTrue(password_verify('SfSm&@8T', $user->getPassword()));
    }

    /**
     * We test the password reset with the same two passwords but not match the needed pattern
     * The Pattern is Lowercase, Uppercase, Digit, Special character
     */
    public function testResetPasswordFormAndNotMatchPasswordPattern(): void
    {
        //on accède à la page de réservation
        $this->client->request('GET', '/fr/user/profile');
        self::assertResponseIsSuccessful();

        //on récupère le formulaire et on remplit les champs
        $this->client->submitForm('Changer le mot de passe', [
            'change_password_form[plainPassword][first]' => 'password',
            'change_password_form[plainPassword][second]' => 'password',
        ]);

        // on vérifie que le changement de mot de passe n'a pas été effectué et qu'un message d'erreur est affiché
        self::assertSelectorExists('div.invalid-feedback');

        // On vérifie que le message d'erreur est le bon
        self::assertSelectorTextContains('div > div.invalid-feedback:nth-of-type(1)', 'Le mot de passe doit contenir des majuscules et minuscules');
        self::assertSelectorTextContains('div > div.invalid-feedback:nth-of-type(2)', 'Le mot de passe doit contenir des chiffres');
        self::assertSelectorTextContains('div > div.invalid-feedback:nth-of-type(3)', 'Le mot de passe doit contenir des caractères spéciaux');
    }

}
