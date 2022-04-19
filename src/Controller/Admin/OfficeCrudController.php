<?php

namespace App\Controller\Admin;

use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;

class OfficeCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public static function getEntityFqcn(): string
    {
        return Office::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Modification of the title translation of all pages
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Gestion des bureaux'))
            ->setPageTitle('edit', new TranslatableMessage('Modification du bureau'))
            ->setPageTitle('new', new TranslatableMessage('Création d\'un bureau'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur le bureau'))
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        // Ajout d'un bouton et d'un action custom pour renvoyer un mail de reinitialisation de MPD
        $duplicatingObject = Action::new('duplicatingObject', new TranslatableMessage('Dupliquer'), 'fa fa-copy')
            ->linkToCrudAction('duplicatingObject')
            ->addCssClass('text-warning');

        return $actions
            // Modification of the translation of the button
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    return $action->setLabel(new TranslatableMessage('Ajouter un bureau'));
                })
            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })
            // add an icon on the button
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            })

            // Added the reset password button on the page
            ->add(Crud::PAGE_INDEX, $duplicatingObject);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            BooleanField::new('enabled', new TranslatableMessage('Activé')),
            TextField::new('name', new TranslatableMessage('Nom')),
            TextField::new('location', new TranslatableMessage('Localisation')),
            TextField::new('floor', new TranslatableMessage('Etage')),
            TextField::new('department', new TranslatableMessage('Service')),

        ];
    }

    public function duplicatingObject(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        // Get the actual office's object class
        $officeObjectToDuplicate = $context->getEntity()->getInstance();

        // Creation of a URL to redirect the USER after he triggers the button
        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $office = clone $officeObjectToDuplicate;
        $office->setName($office->getName() . ' (copie)');
        $this->entityManager->persist($office);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le bureau a bien été dupliqué');

        return $this->redirect($url);
    }
}
