<?php

namespace App\Form;

use App\Repository\MeetingRoomRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseMeetingRoomFormType extends AbstractType
{

    private MeetingRoomRepository $meetingRoomRepository;

    public function __construct(MeetingRoomRepository $meetingRoomRepository)
    {
        $this->meetingRoomRepository = $meetingRoomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On récupère les salles qui sont active
        $activeMeetingRooms = $this->meetingRoomRepository->findActiveMeetingRoom();

        $meetingRoomByLocations = [];
        foreach ($activeMeetingRooms as $meetingRoom)
        {
            $meetingRoomByLocations[$meetingRoom->getLocation()][$meetingRoom->getName()] = $meetingRoom->getId();
        }

        $builder
            ->add('meetingRoom', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => $meetingRoomByLocations,
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
                'label' => 'Rechercher'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
