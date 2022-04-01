<?php

namespace App\DataFixtures;

use App\Entity\Office;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OfficeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $location = array('Nantes', 'Paris', 'Lyon', 'Marseille');
        $service = array('Développement', 'Réseau', 'Administration', 'Comptabilité');

        for ($i = 0; $i < 10; $i++) {
            $office = new Office();

            $this->addReference('office_' . $i, $office);

            $office->setName('Bureau '.$i);
            $office->setLocation($faker->randomElement($location));
            $office->setFloor('Etage '.$i);
            $office->setDepartment($faker->randomElement($service));
            $office->setEnabled(true);

            $manager->persist($office);
        }

        $manager->flush();
    }

}
