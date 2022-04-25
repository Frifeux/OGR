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
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;

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
                    'label' => new TranslatableMessage('Nouveau mot de passe')
                ],
                'second_options' => [
                    'label' => new TranslatableMessage('Retapez le nouveau mot de passe')
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => new TranslatableMessage('Entrez un mot de passe'),
                    ]),
                    new RollerworksPassword\PasswordRequirements([
                        'minLength' => $_ENV['PASSWORD_MIN_LENGTH'],
                        'requireLetters' => $_ENV['PASSWORD_REQUIRE_LETTERS'],
                        'requireNumbers' => $_ENV['PASSWORD_REQUIRE_NUMBERS'],
                        'requireCaseDiff' => $_ENV['PASSWORD_REQUIRE_CASE_DIFF'],
                        'tooShortMessage' => new TranslatableMessage("Le mot de passe doit contenir au moins {{length}} caractères"),
                        'missingLettersMessage' => new TranslatableMessage("Le mot de passe doit contenir des lettres"),
                        'requireCaseDiffMessage' => new TranslatableMessage("Le mot de passe doit contenir des majuscules et minuscules"),
                        'missingNumbersMessage' => new TranslatableMessage("Le mot de passe doit contenir des chiffres"),
                    ]),
                ],
                'invalid_message' => new TranslatableMessage('Les deux mots de passe doivent être identiques !'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
