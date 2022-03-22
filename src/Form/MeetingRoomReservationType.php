<?php

namespace App\Form;

use App\Entity\MeetingRoomReservation;
use App\Repository\MeetingRoomRepository;
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
                'attr' => [
                    'placeholder' => new translatableMessage('Le nom de votre réservation'),
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => new translatableMessage('Entrez l\'intitulé de la réservation'),
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => new TranslatableMessage('Description'),
                'attr' => [
                    'placeholder' => new translatableMessage('La description de votre réservation'),
                ],
            ])
            ->add('meetingRoom', ChoiceType::class, [
                'mapped' => false,
                'label' => new TranslatableMessage('Sélection de la salle'),
                'choices' => $this->meetingRoomRepository->getMeetingRoomByLocation(),
            ])

            // TODO: Voir pourquoi les label ne fonctionne pas !!

            ->add('startAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de début'),
//                'date_label' => new TranslatableMessage('Date de début'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de début'),
                'hours' => range(8,20),
                'minutes' => [0,30],
            ])

            ->add('endAt', DateTimeType::class,[
                'label' => new TranslatableMessage('Date et Heure de fin'),
//                'date_label' => new TranslatableMessage('Date de fin'),
                'date_widget' => 'single_text',
//                'time_label' => new TranslatableMessage('Heure de fin'),
                'hours' => range(8,20),
                'minutes' => [0,30],
            ])

            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Réserver'),
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
