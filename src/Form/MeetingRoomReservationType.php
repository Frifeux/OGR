<?php

namespace App\Form;

use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use App\Repository\MeetingRoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MeetingRoomReservationType extends AbstractType
{

    private MeetingRoomRepository $meetingRoomRepository;

    public function __construct(MeetingRoomRepository $meetingRoomRepository)
    {
        $this->meetingRoomRepository = $meetingRoomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => new TranslatableMessage('Titre'),
                'label_attr' => [
                    'class' => 'small',
                ],
                'attr' => [
                    'class' => 'form-control-sm',
                    'placeholder' => new TranslatableMessage('Le titre de votre réservation'),
                ],
                'row_attr' => [
                    'class' => 'mb-2',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => new TranslatableMessage('Entrez l\'intitulé de la réservation'),
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => new TranslatableMessage('Description'),
                'label_attr' => [
                    'class' => 'small',
                ],
                'attr' => [
                    'class' => 'form-control-sm',
                    'placeholder' => new TranslatableMessage('La description de votre réservation'),
                ],
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])
            ->add('meetingRoom', EntityType::class, [
                'label' => new TranslatableMessage('Sélection de la salle'),
                'class' => MeetingRoom::class,
                'label_attr' => [
                    'class' => 'small',
                ],
                'attr' => [
                    'class' => 'form-select-sm',
                ],
                'choices' => $this->meetingRoomRepository->getMeetingRoomByLocation(),
                'row_attr' => [
                    'class' => 'mb-2',
                ],
            ])

            // TODO: Voir pourquoi les label ne fonctionne pas !!

            ->add('startAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de début'),
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
//                'date_label' => new TranslatableMessage('Date de fin'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de fin'),
                'hours' => range($_ENV['WORKING_HOURS_START'],$_ENV['WORKING_HOURS_END']),
                'minutes' => [0,30],
            ])

            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Réserver'),
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MeetingRoomReservation::class,
        ]);
    }
}
