<?php

namespace App\Controller\Admin;

use App\Entity\OfficeReservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Translation\TranslatableMessage;

class OfficeReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OfficeReservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Modification of the title translation of all pages
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Réservations des bureaux'))
            ->setPageTitle('edit', new TranslatableMessage('Modification d\'une réservation'))
            ->setPageTitle('new', new TranslatableMessage('Ajout d\'une réservation'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur la réservation'))
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Suppression of the button new object
            ->remove(Crud::PAGE_INDEX, Action::NEW)

            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextAreaField::new('description', new TranslatableMessage('Description')),
            DateTimeField::new('startAt', new TranslatableMessage('Commence le')),
            DateTimeField::new('endAt', new TranslatableMessage('Finit le')),

            // Besoin d'ajouter la fonction __toString() pour le champ AssociationField, exemple:
            // Classe Office
            // public function __toString(): string
            //    {
            //        return $this->name;
            //    }
            AssociationField::new('user',new TranslatableMessage('Utilisateur')),
            AssociationField::new('office',new TranslatableMessage('Bureau')),
        ];
    }
}
