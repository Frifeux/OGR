<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
        $resolver->setDefaults([]);
    }
}
