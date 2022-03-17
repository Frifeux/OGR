<?php

namespace App\Controller\Admin;

use App\Entity\EquipmentReservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Translation\TranslatableMessage;

class EquipmentReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EquipmentReservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Réservations des matériels'))
            ->setPageTitle('edit', new TranslatableMessage('Modification d\'une réservation'))
            ->setPageTitle('new', new TranslatableMessage('Ajout d\'une réservation'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur la réservation'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    return $action->setLabel(new TranslatableMessage('Ajouter une réservation'));
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
            TextAreaField::new('description', new TranslatableMessage('Description')),
            DateTimeField::new('startAt', new TranslatableMessage('Commence le')),
            DateTimeField::new('endAt', new TranslatableMessage('Finit le')),

            // Besoin d'ajouter la fonction __toString() pour le champ AssociationField, exemple:
            // Classe Equipment
            // public function __toString(): string
            //    {
            //        return $this->name;
            //    }
            AssociationField::new('user',new TranslatableMessage('Utilisateur')),
            AssociationField::new('equipment',new TranslatableMessage('Matériel')),
        ];
    }
}
