<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class UserCrudController extends AbstractCrudController
{

    private ResetPasswordHelperInterface $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {

        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', new TranslatableMessage('Gestion des utilisateurs'))
            ->setPageTitle('edit', new TranslatableMessage('Modification de l\'utilisateur'))
            ->setPageTitle('new', new TranslatableMessage('Création d\'un utilisateur'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur l\'utilisateur'));
    }

    public function configureActions(Actions $actions): Actions
    {

        // Ajout d'un bouton et d'un action custom
        $sendResetPassword = Action::new('sendResetPassword', new TranslatableMessage('Réinitialiser le mot de passe'), 'fa fa-file-invoice')
            ->linkToCrudAction('sendResetPassword')
            ->setCssClass('btn btn-danger');


        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    // Message Custom avec mise en place de traduction
                    $create_button_message = new TranslatableMessage('Créer un utilisateur');
                    return $action->setLabel($create_button_message->getMessage());
                })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setIcon('fa fa-edit');
            })

            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setIcon('fa fa-trash');
            })

            ->add(Crud::PAGE_EDIT, $sendResetPassword);
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('firstname', new TranslatableMessage('Prénom')),
            TextField::new('lastname', new TranslatableMessage('Nom')),
            EmailField::new('email', new TranslatableMessage('Email')),
            TextField::new('password', new TranslatableMessage('Mot de passe'))
                ->setFormType(PasswordType::class)
                ->hideWhenUpdating()
                ->hideOnDetail()
                ->hideOnIndex(),
            TextField::new('location', new TranslatableMessage('Localisation')),
            ChoiceField::new(new TranslatableMessage('Roles'))
                ->allowMultipleChoices()
                ->setChoices(
                    [
                        (new TranslatableMessage('Utilisateur'))->getMessage() => 'ROLE_USER',
                        (new TranslatableMessage('Administrateur'))->getMessage() => 'ROLE_ADMIN',
                    ]
                ),
            DateTimeField::new('createdAt', new TranslatableMessage('Créé le'))
                ->onlyOnDetail(),
        ];
    }


    // Video aide: https://www.youtube.com/watch?v=ze6XJTACo1s
    public function sendResetPassword(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, MailerInterface $mailer): Response
    {
        $user = $context->getEntity()->getInstance();

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($user->getId())
            ->generateUrl();

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', new TranslatableMessage('Un mail de réinitialisation de mot de passe à déjà été envoyé à ' . $user->getEmail()));

            return $this->redirect($url);
        }

        // TODO: Modifier les paramètres de mail, créer des variables d'environement
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@ogr.fr', 'OGR Reset Password'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $this->addFlash('success', new TranslatableMessage('Un mail de réinitialisation de mot de passe à bien été envoyé à ' . $user->getEmail()));

        $mailer->send($email);

        return $this->redirect($url);
    }
}
