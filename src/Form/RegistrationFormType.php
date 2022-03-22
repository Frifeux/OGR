<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => new TranslatableMessage('Nom'),
                'constraints' => [
                    new NotBlank([
                        'message' => new TranslatableMessage('Entrez votre nom'),
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => new TranslatableMessage('Prénom'),
                'constraints' => [
                    new NotBlank([
                        'message' => new TranslatableMessage('Entrez votre prénom.'),
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => new TranslatableMessage('Email'),
                'constraints' => [
                    new NotBlank([
                        'message' => new TranslatableMessage('Entrez une adresse email.'),
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('J\'accepte les conditions générales d\'utilisation'),
                'constraints' => [
                    new IsTrue([
                        'message' => new TranslatableMessage('Vous devez acceptez les conditions d\'utilisations du site.'),
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => [
                    'label' => new translatableMessage('Nouveau mot de passe')
                ],
                'second_options' => [
                    'label' => new translatableMessage('Retapez le nouveau mot de passe')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => new translatableMessage('Entrez un mot de passe'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => new translatableMessage('Votre mot de passe doit faire plus de {{ limit }} caractères'),
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => new translatableMessage('Les deux mots de passe doivent être identiques !'),

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
