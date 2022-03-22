<?php

namespace App\Form;

use App\Repository\MeetingRoomRepository;
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
            ->add('meetingRoom', ChoiceType::class, [
                'label' => new TranslatableMessage('Sélection de la salle'),
                'choices' => $this->meetingRoomRepository->getMeetingRoomByLocation(),
            ])
            ->add('save', SubmitType::class, [
                'label' => new TranslatableMessage('Voir les réservations')
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
