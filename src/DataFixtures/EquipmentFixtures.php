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

        $location = array('Nantes', 'Paris', 'Lyon', 'Marseille');
        $equipment_type = array('Ordinateur', 'Tablette', 'Téléphone', 'Smartphone');

        // generate 4 equipments
        for ($i = 0; $i < 4; $i++) {
            $equipment = new Equipment();

            $this->addReference('equipment_' . $i, $equipment);

            $equipment->setName('Matériel '.$i);
            $equipment->setType($equipment_type[$i]);
            $equipment->setLocation($location[$i]);
            $equipment->setEnabled(true);

            $manager->persist($equipment);
        }

        $manager->flush();
    }

}
