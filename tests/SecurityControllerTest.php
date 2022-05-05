<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends KernelTestCase
{

    /**
     * @param KernelBrowser $client
     * @param UserRepository $userRepository
     */
    public function login(KernelBrowser $client, UserRepository $userRepository): void
    {

        // get the admin user in the database
        $user = $userRepository->findOneBy(['email' => 'Admin.DOE@mail.com']);

        // create a token for the admin user
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, ['main'], $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        // create a cookie for the session
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
