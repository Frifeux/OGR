<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * This function is called when the event is triggered
     */
    public static function getSubscribedEvents() :  array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
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

    // On hash le MDP de l'utilisateur avant d'écrire notre entrée dans la BDD
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

}