<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $location = array('Nantes', 'Paris', 'Lyon', 'Marseille');
        $equipment_type = array('Ordinateur', 'Tablette', 'Téléphone', 'Smartphone');

        // generate 10 equipments
        for ($i = 0; $i < 10; $i++) {
            $equipment = new Equipment();

            $this->addReference('equipment_' . $i, $equipment);

            $equipment->setName('Equipement '.$i);
            $equipment->setType($faker->randomElement($equipment_type));
            $equipment->setLocation($faker->randomElement($location));
            $equipment->setEnabled(true);

            $manager->persist($equipment);
        }

        $manager->flush();
    }

}
