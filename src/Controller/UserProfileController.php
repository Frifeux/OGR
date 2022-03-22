<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Translation\TranslatableMessage;

class UserProfileController extends AbstractController
{

    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * This function is used to change the password of the user
     */
    #[Route('/user/profile', name: 'user_profile')]
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
            // récupération de notre objet utilisateur
            $user = $this->security->getUser();

            // Création de notre formulaire
            $passwordUserForm = $this->createForm(ChangePasswordFormType::class);
            $passwordUserForm->handleRequest($request);

            if ($passwordUserForm->isSubmitted() && $passwordUserForm->isValid()) {

                // Encode(hash) the plain password, and set it.
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $passwordUserForm->get('plainPassword')->getData()
                );

                $user->setPassword($encodedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', new TranslatableMessage("Vous avez bien changé de mot de passe !"));

            }

            return $this->render('user/profile.html.twig', [
                'passwordForm' => $passwordUserForm->createView(),
                'user' => $user,
            ]);

    }




}
