<?php

namespace App\Controller;

use App\Form\ChooseMeetingRoomFormType;
use App\Repository\MeetingRoomRepository;
use App\Repository\MeetingRoomReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeetingRoomController extends AbstractController
{
    #[Route('/reservation/metting_room', name: 'metting_room')]
    public function mettingRoom(Request $request, MeetingRoomReservationRepository $meetingRoomReservationRepository): Response
    {

        $form = $this->createForm(ChooseMeetingRoomFormType::class);
        $form->handleRequest($request);

        $jsonifyMeetingRoomReservation = [];
        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les RDV de la salles selectionné
            $meetingRoomReservations = $meetingRoomReservationRepository->findBy(['meetingRoom' => $form->get('meetingRoom')->getData()]);
            foreach($meetingRoomReservations as $meetingRoomReservation)
            {
                $jsonifyMeetingRoomReservation[] = [
                    'id' => $meetingRoomReservation->getId(),
                    'start' => $meetingRoomReservation->getStartAt()->format('Y-m-d H:i:s'),
                    'end' => $meetingRoomReservation->getEndAt()->format('Y-m-d H:i:s'),
                    'title' => $meetingRoomReservation->getTitle(),
                    'description' => $meetingRoomReservation->getDescription(),
                    'backgroundColor' => '#0053a3',
                    'borderColor' => '#0053a3',
                ];
            }
        }

        return $this->render('reservation/meeting_room.html.twig', [
            'chooseMeetingRoom' => $form->createView(),
            'meetingRoomReservations' => json_encode($jsonifyMeetingRoomReservation),
        ]);
    }

}
