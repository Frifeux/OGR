<?php

namespace App\Form;

use App\Entity\MeetingRoom;
use App\Repository\MeetingRoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class ChooseMeetingRoomFormType extends AbstractType
{

    private MeetingRoomRepository $meetingRoomRepository;

    public function __construct(MeetingRoomRepository $meetingRoomRepository)
    {
        $this->meetingRoomRepository = $meetingRoomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('meetingRoom', EntityType::class, [
                'label' => new TranslatableMessage('Sélection de la salle'),
                'class' => MeetingRoom::class,
                'choices' => $this->meetingRoomRepository->getMeetingRoomByLocation(),
                'label_attr' => [
                    'class' => 'small',
                ],
                'attr' => [
                    'class' => 'form-select-sm',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Voir les réservations'),
                'attr' => [
                    'class' => 'btn btn-primary btn-sm'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
