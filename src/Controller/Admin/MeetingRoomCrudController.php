<?php

namespace App\Controller\Admin;

use App\Entity\MeetingRoom;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;

class MeetingRoomCrudController extends AbstractCrudController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return MeetingRoom::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Modification of the title translation of all pages
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Gestion des salles'))
            ->setPageTitle('edit', new TranslatableMessage('Modification de la salle'))
            ->setPageTitle('new', new TranslatableMessage('Création d\'une salle'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur la salle'))
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
                    return $action->setLabel(new TranslatableMessage('Ajouter une salle'));
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
            IntegerField::new('capacity', new TranslatableMessage('Capacité')),

        ];
    }

    public function duplicatingObject(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        // Get the actual MeetingRoom's object class
        $meetingRoomObjectToDuplicate = $context->getEntity()->getInstance();

        // Creation of a URL to redirect the USER after he triggers the button
        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        // Creation of a new object with the same values as the actual one
        $meetingRoom = clone $meetingRoomObjectToDuplicate;
        $meetingRoom->setName($meetingRoom->getName() . ' (copie)');
        $this->entityManager->persist($meetingRoom);
        $this->entityManager->flush();

        $this->addFlash('success', new TranslatableMessage('La salle a bien été dupliquée'));

        return $this->redirect($url);
    }
}
