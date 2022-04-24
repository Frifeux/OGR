<?php

namespace App\Controller\Admin;

use App\Entity\Office;
use App\Repository\OfficeRepository;
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
    private OfficeRepository $officeRepository;

    public function __construct(EntityManagerInterface $entityManager, OfficeRepository $officeRepository)
    {
        $this->entityManager = $entityManager;
        $this->officeRepository = $officeRepository;
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
        // Ajout d'un bouton et d'un action custom pour dupliquer un objet
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

        // Creation of a new object with the same values as the actual one
        $office = clone $officeObjectToDuplicate;

        // Change the name of the object to avoid duplicates in the database (the name is unique)

        // Get the number in the name of the object if there is one
        // If there is no number, the number is set to 1. We used the preg_match function to get the number
        $number = preg_match('/\((\d+)\)/', $office->getName(), $matches) ? $matches[1] : 1;

        // We remove the number in the name of the object if there is one
        $office->setName(preg_replace('/ \((\d+)\)/', '', $office->getName()));
        $newName = $office->getName() . ' (' . ($number) . ')';

        // The number is incremented if the name already exists in the database (ex: "Office (1)", "Office (2)", ...)
        while ($this->officeRepository->findOneBy(['name' => $newName])) {
            $number++;
            $newName = $office->getName() . ' (' . ($number) . ')';
        }

        $office->setName($newName);

        $this->entityManager->persist($office);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('Le bureau a bien été dupliqué'));

        return $this->redirect($url);
    }
}
