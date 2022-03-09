<?php
namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
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

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setUsersRegister'],
        ];
    }

    public function setUsersRegister(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $bytes = openssl_random_pseudo_bytes(6);
        $plainpassword = bin2hex($bytes); // 6 bytes converti en hexa donne 12 caractères

        $hashedPassword = $this->passwordHasher->hashPassword(
            $entity,
            $plainpassword
        );

        // On met en Majuscule le nom de famille
        $entity->setLastname(strtoupper($entity->getLastname()));

        // Définit la date à laquel le compte a été créé
        $entity->setCreatedAt(new \DateTimeImmutable("now"));

        $entity->setPassword($hashedPassword);
    }
}