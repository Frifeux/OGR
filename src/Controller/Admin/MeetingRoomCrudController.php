<?php

namespace App\Controller\Admin;

use App\Entity\MeetingRoom;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class MeetingRoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MeetingRoom::class;
    }

    public function configureCrud(Crud $crud): Crud
    {

        return $crud
            ->setPageTitle('index', new TranslatableMessage('Gestion des salles'))
            ->setPageTitle('edit', new TranslatableMessage('Modification de la salle'))
            ->setPageTitle('new', new TranslatableMessage('Création d\'une salle'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur la salle'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    return $action->setLabel(new TranslatableMessage('Ajouter une salle'));
                })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })

            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            BooleanField::new('enabled', new TranslatableMessage('Activé')),
            TextField::new('name', new TranslatableMessage('Nom')),
            TextField::new('location', new TranslatableMessage('Localisation')),

        ];
    }
}
