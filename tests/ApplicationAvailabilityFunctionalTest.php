<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProviderWhenUserIsConnectedUser
     * It tests that a user who is not connected is redirected to the login page when trying to access a page that requires
     * authentication
     *
     */
    public function testPageRedirectionWithoutBeingConnected($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(302);

        //Verify that the user is redirected to the login page
        self::assertStringContainsString('/fr/login', $client->getResponse()->getTargetUrl());
    }

    /**
     * @dataProvider urlProviderWhenUserNotConnected
     * It tests that the page is accessible without being connected
     *
     */
    public function testPageWithoutBeingConnected($url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider urlProviderWhenUserIsConnectedUser
     * It tests that the page is accessible when the user is connected
     *
     */
    public function testPageWhenUserIsConnected($url): void
    {
        $client = self::createClient();

        //on récupère l'utilisateur et on le connecte
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'Admin.DOE@mail.com']);
        $client->loginUser($testUser);

        $client->request('GET', $url);

        // on vérifie que la page est bien chargée
        // Si on accède à la page d'administration alors on a une redirection 302
        // Pour le reste on a une réponse 200
        if ($url === '/fr/admin'){
            self::assertResponseStatusCodeSame(302);
        } else {
            self::assertResponseIsSuccessful();
        }

    }

    /**
     * It returns an array of URL that should be accessible to a user who is not connected
     */
    public function urlProviderWhenUserNotConnected(): \Generator
    {
        yield ['/fr/login'];
        yield ['/fr/register'];
        yield ['/fr/reset-password'];
    }

    /**
     * It returns an array of URL that should be accessible to a user who is connected
     */
    public function urlProviderWhenUserIsConnectedUser(): \Generator
    {
        yield ['/fr/home'];
        yield ['/fr/meeting-room'];
        yield ['/fr/office'];
        yield ['/fr/equipment'];
        yield ['/fr/reservation'];
        yield ['/fr/user/profile'];
        yield ['/fr/admin'];
    }
}
