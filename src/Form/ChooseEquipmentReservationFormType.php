<?php

namespace App\Form;

use App\Repository\EquipmentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class ChooseEquipmentReservationFormType extends AbstractType
{

    private EquipmentRepository $equipmentRepository;

    public function __construct(EquipmentRepository $equipmentRepository)
    {
        $this->equipmentRepository = $equipmentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection de l\'emplacement'),
                'required' => false,
                'choices' => $this->equipmentRepository->getAllLocation(),
            ])
            ->add('type', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection du type'),
                'required' => false,
                'choices' => $this->equipmentRepository->getAllType(),
            ])

            // TODO: Voir pourquoi les label ne fonctionne pas !!
            ->add('startAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de début'),
                'required' => true,
//                'date_label' => new TranslatableMessage('Date de début'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de début'),
                'hours' => range($_ENV['WORKING_HOURS_START'],$_ENV['WORKING_HOURS_END']),
                'minutes' => [0,30],
            ])

            ->add('endAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de fin'),
                'required' => true,
//                'date_label' => new TranslatableMessage('Date de fin'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de fin'),
                'hours' => range($_ENV['WORKING_HOURS_START'],$_ENV['WORKING_HOURS_END']),
                'minutes' => [0,30],
            ])

            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Voir les disponibilités'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
