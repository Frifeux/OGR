<?php

namespace App\Controller\Admin;

use App\Entity\OfficeReservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OfficeReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OfficeReservation::class;
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
