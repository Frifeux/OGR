<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserProfileFormType;
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

    #[Route('/user/profile', name: 'user_profile')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
            $user = $this->security->getUser();

            // Form infos utilisateur
            $userInfoForm = $this->createForm(UserProfileFormType::class, $user);
            $userInfoForm->handleRequest($request);

            if ($userInfoForm->isSubmitted() && $userInfoForm->isValid()) {

                $this->addFlash('success', new TranslatableMessage("Vos paramètres ont bien été enregistrés !"));

                $this->entityManager->persist($user);
                $this->entityManager->flush();

            }

            $passwordUserForm = $this->createForm(ChangePasswordFormType::class);
            $passwordUserForm->handleRequest($request);

            if ($passwordUserForm->isSubmitted() && $passwordUserForm->isValid()) {

                $this->addFlash('success', new TranslatableMessage("Vous avez bien changé de mot de passe !"));

                // Encode(hash) the plain password, and set it.
                $encodedPassword = $userPasswordHasher->hashPassword(
                    $user,
                    $passwordUserForm->get('plainPassword')->getData()
                );

                $user->setPassword($encodedPassword);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

            }

            return $this->render('user/profile.html.twig', [
                'profileForm' => $userInfoForm->createView(),
                'passwordForm' => $passwordUserForm->createView(),
            ]);

    }




}
