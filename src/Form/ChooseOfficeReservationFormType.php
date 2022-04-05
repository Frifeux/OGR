<?php

namespace App\Form;

use App\Entity\MeetingRoom;
use App\Entity\Office;
use App\Entity\OfficeReservation;
use App\Repository\MeetingRoomRepository;
use App\Repository\OfficeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Choice;

class ChooseOfficeReservationFormType extends AbstractType
{

    private OfficeRepository $officeRepository;

    public function __construct(OfficeRepository $officeRepository)
    {
        $this->officeRepository = $officeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('location', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection de l\'emplacement'),
                'required' => false,
                'choices' => $this->officeRepository->getAllLocation(),
                'label_attr' => [
                    'class' => 'small',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('floor', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection de l\'étage'),
                'required' => false,
                'choices' => $this->officeRepository->getAllFloor(),
                'label_attr' => [
                    'class' => 'small',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('department', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection du service'),
                'required' => false,
                'choices' => $this->officeRepository->getAllDepartment(),
                'label_attr' => [
                    'class' => 'small',
                ],
                'row_attr' => [
                    'class' => 'mb-1',
                ],
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])

            // TODO: Voir pourquoi les label ne fonctionne pas !!
            ->add('startAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de début'),
                'required' => true,
//                'date_label' => new TranslatableMessage('Date de début'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de début'),
                'hours' => range(8,20),
                'minutes' => [0,30],
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])

            ->add('endAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de fin'),
                'required' => true,
//                'date_label' => new TranslatableMessage('Date de fin'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de fin'),
                'hours' => range($_ENV['WORKING_HOURS_START'],$_ENV['WORKING_HOURS_END']),
                'minutes' => [0,30],
//                'by_reference' => true, //The DateTime classes are treated as immutable objects.
            ])

            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Voir les disponibilités'),
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
