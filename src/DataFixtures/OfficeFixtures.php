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

        $location = array('Nantes', 'Paris', 'Lyon', 'Marseille');
        $service = array('Développement', 'Réseau', 'Administration', 'Comptabilité');
        $floor = array('1er', '2ème', '3ème', '4ème');

        for ($i = 0; $i < 4; $i++) {
            $office = new Office();

            $this->addReference('office_' . $i, $office);

            $office->setName('Bureau '.$i);
            $office->setLocation($location[$i]);
            $office->setFloor($floor[$i]);
            $office->setDepartment($service[$i]);
            $office->setEnabled(true);

            $manager->persist($office);
        }

        $manager->flush();
    }

}
