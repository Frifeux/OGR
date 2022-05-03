<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\EmailVerifier;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\TranslatableMessage;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private EmailVerifier $emailVerifier;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EmailVerifier $emailVerifier)
    {
        $this->passwordHasher = $passwordHasher;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * This function is called when the event is triggered
     */
    public static function getSubscribedEvents() :  array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
            AfterEntityPersistedEvent::class => ['sendConfirmationEmail'],
        ];
    }

    // Fonction pour génération d'un mot de passe aléatoire
    private function randomPassword(int $lenght): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";

        // Si la taille est supérieur à 4095 on le force à rester en dessous pour des questions de sécurité symfony
        if ($lenght > 4095){ $lenght = 4095; }
        return substr(str_shuffle($chars), 0, $lenght);
    }

    // On hash le MDP de l'utilisateur avant d'écrire notre entrée dans la BDD puis ont lui envoie un mail de confirmation de son inscription
    public function addUser(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $entity,
            $entity->getPassword(),
        );

        $entity->setPassword($hashedPassword);
    }

    // On envoie un mail de confirmation de l'inscription
    public function sendConfirmationEmail(AfterEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $entity,
            (new TemplatedEmail())
                ->from(new Address($_ENV['ADDRESS_FROM'], 'OGR'))
                ->to($entity->getEmail())
                ->subject(new TranslatableMessage('Confirmation de votre inscription'))
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

}