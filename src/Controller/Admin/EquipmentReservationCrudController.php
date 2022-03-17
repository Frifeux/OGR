<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentReservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EquipmentReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EquipmentReservation::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
