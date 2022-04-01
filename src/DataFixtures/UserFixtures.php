<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        // generate 5 basic users
        for ($i = 0; $i < 5; $i++) {

            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);

            $this->addReference('user_' . $i, $user);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $user->setLocation($faker->city);
            $user->setIsVerified($faker->numberBetween(0, 1));

            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
        }

        // generate admin user
        $user = new User();
        $user->setFirstname('Admin');
        $user->setLastname('Doe');
        $user->setEmail($user->getFirstname(). '.' . $user->getLastname() .'@mail.com');

        $this->addReference('admin_user', $user);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);

        $user->setLocation($faker->city);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $manager->persist($user);

        // On persiste toutes nos données
        $manager->flush();
    }
}
