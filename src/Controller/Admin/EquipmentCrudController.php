<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;

class EquipmentCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Modification of the title translation of all pages
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Gestion des matériels'))
            ->setPageTitle('edit', new TranslatableMessage('Modification du matériel'))
            ->setPageTitle('new', new TranslatableMessage('Création d\'un matériel'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur le matériel'))
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        // Ajout d'un bouton et d'un action custom pour dupliquer un objet
        $duplicatingObject = Action::new('duplicatingObject', new TranslatableMessage('Dupliquer'), 'fa fa-copy')
            ->linkToCrudAction('duplicatingObject')
            ->addCssClass('text-warning');

        return $actions
            // Modification of the translation of the button
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    return $action->setLabel(new TranslatableMessage('Ajouter un matériel'));
                })
            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            })
            // Added the duplicating button on the page
            ->add(Crud::PAGE_INDEX, $duplicatingObject);

    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            BooleanField::new('enabled', new TranslatableMessage('Activé')),
            TextField::new('name', new TranslatableMessage('Nom')),
            TextField::new('type', new TranslatableMessage('Type')),
            TextField::new('location', new TranslatableMessage('Localisation')),

        ];
    }

    public function duplicatingObject(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        // Get the actual office's object class
        $equipmentObjectToDuplicate = $context->getEntity()->getInstance();

        // Creation of a URL to redirect the USER after he triggers the button
        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        // Creation of a new object with the same values as the actual one
        $equipment = clone $equipmentObjectToDuplicate;
        $equipment->setName($equipment->getName() . ' (copie)');
        $this->entityManager->persist($equipment);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('L\'équipement a bien été dupliqué'));

        return $this->redirect($url);
    }
}
