<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
        // Modification of the title translation of all pages
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Gestion des utilisateurs'))
            ->setPageTitle('edit', new TranslatableMessage('Modification de l\'utilisateur'))
            ->setPageTitle('new', new TranslatableMessage('Cr??ation d\'un utilisateur'))
            ->setPageTitle('detail', new TranslatableMessage('Informations sur l\'utilisateur'))
            ->showEntityActionsInlined();

    }

    public function configureActions(Actions $actions): Actions
    {

        // Ajout d'un bouton et d'un action custom pour renvoyer un mail de reinitialisation de MPD
        $sendResetPassword = Action::new('sendResetPassword', new TranslatableMessage('R??initialiser le mot de passe'), 'fa fa-file-invoice')
            ->linkToCrudAction('sendResetPassword')
            ->setCssClass('btn btn-danger');


        return $actions
            // Modification of the translation of the button
            ->update(Crud::PAGE_INDEX, Action::NEW,
                function (Action $action) {
                    return $action->setLabel(new TranslatableMessage('Cr??er un utilisateur'));
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
            ->add(Crud::PAGE_EDIT, $sendResetPassword);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            BooleanField::new('enabled', new TranslatableMessage('Activ??')),
            TextField::new('firstname', new TranslatableMessage('Pr??nom')),
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
            DateTimeField::new('createdAt', new TranslatableMessage('Cr???? le'))
                ->onlyOnDetail(),
        ];
    }


    // help Video: https://www.youtube.com/watch?v=ze6XJTACo1s
    //
    public function sendResetPassword(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, MailerInterface $mailer): Response
    {
        // Get the user's object class
        $user = $context->getEntity()->getInstance();

        // Creation of a URL to redirect the USER after he triggers the button
        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($user->getId())
            ->generateUrl();

        // Creation of a token to allow the user to reset his own password
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', new TranslatableMessage('Un mail de r??initialisation de mot de passe ?? d??j?? ??t?? envoy?? ?? ' . $user->getEmail()));

            return $this->redirect($url);
        }

        // Sending an email to the user with the link to reset its own password
        $email = (new TemplatedEmail())
            ->from(new Address($_ENV['ADDRESS_FROM'], 'OGR'))
            ->to($user->getEmail())
            ->subject(new TranslatableMessage('R??initialisation de votre mot de passe'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);
        $mailer->send($email);

        $this->addFlash('success', new TranslatableMessage('Un mail de r??initialisation de mot de passe ?? bien ??t?? envoy?? ?? ' . $user->getEmail()));

        return $this->redirect($url);
    }
}
