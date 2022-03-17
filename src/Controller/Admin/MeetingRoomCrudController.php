<?php

namespace App\Controller\Admin;

use App\Entity\MeetingRoom;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MeetingRoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MeetingRoom::class;
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
