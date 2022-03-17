<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;
    private $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public static function getSubscribedEvents() :  array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
        ];
    }

    // Génération d'un mot de passe aléatoire
    private function randomPassword(int $lenght): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";

        // Si la taille est supérieur à 4095 on le force à rester en dessous pour des questions de sécurité symfony
        if ($lenght > 4095){ $lenght = 4095; }
        return substr(str_shuffle($chars), 0, $lenght);
    }

    // On hash le MDP de l'utilisateur
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

        // On met en Majuscule le nom de famille
        $entity->setLastname(strtoupper($entity->getLastname()));

        // Définit la date à laquel le compte a été créé
        $entity->setCreatedAt(new \DateTimeImmutable("now"));

        $entity->setPassword($hashedPassword);
    }

}