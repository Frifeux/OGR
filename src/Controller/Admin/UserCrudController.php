<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Message Custom avec mise en place de traduction
        $index_message = new TranslatableMessage('Gestion des utilisateurs');
        $edit_message = new TranslatableMessage('Modification de l\'utilisateur');
        $new_message = new TranslatableMessage('Création d\'un utilisateur');
        $detail_message = new TranslatableMessage('Informations sur l\'utilisateur');

        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', $index_message->getMessage())
            ->setPageTitle('edit', $edit_message->getMessage())
            ->setPageTitle('new', $new_message->getMessage())
            ->setPageTitle('detail', $detail_message->getMessage());
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {

                    // Message Custom avec mise en place de traduction
                    $create_button_message = new TranslatableMessage('Créer un utilisateur');
                    return $action->setLabel($create_button_message->getMessage());
                })

            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit')->addCssClass('btn btn-outline-warning');
            });
    }

    public function configureFields(string $pageName): iterable
    {

        // Traduction des champs
        $firstname_user_field = new TranslatableMessage('Prénom');
        $lastname_user_field = new TranslatableMessage('Nom');
        $email_user_field = new TranslatableMessage('Email');
        $roles_user_field = new TranslatableMessage('Roles');
        $creationDate_user_field = new TranslatableMessage('Créé le');
        $role_name_user = new TranslatableMessage('Utilisateur');
        $role_name_administrator = new TranslatableMessage('Administrateur');

        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('firstname', $firstname_user_field->getMessage()),
            TextField::new('lastname', $lastname_user_field->getMessage()),
            EmailField::new('email', $email_user_field->getMessage()),
//            TextField::new('password', 'Mot de passe')
//                ->setFormType(PasswordType::class)
//                ->hideOnIndex(),
            ChoiceField::new($roles_user_field->getMessage())
                ->allowMultipleChoices()
                ->setChoices(
                    [
                        $role_name_user->getMessage() => 'ROLE_USER',
                        $role_name_administrator->getMessage() => 'ROLE_ADMIN',
                    ]
                ),
            DateTimeField::new('createdAt', $creationDate_user_field->getMessage())
                ->onlyOnDetail(),
        ];
    }
}
