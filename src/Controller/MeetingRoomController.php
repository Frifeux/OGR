<?php

namespace App\Controller;

use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use App\Form\ChooseMeetingRoomFormType;
use App\Form\MeetingRoomReservationType;
use App\Repository\MeetingRoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class MeetingRoomController extends AbstractController
{

    private MeetingRoomReservationRepository $meetingRoomReservationRepository;

    public function __construct(MeetingRoomReservationRepository $meetingRoomReservationRepository)
    {
        $this->meetingRoomReservationRepository = $meetingRoomReservationRepository;
    }

    public function getReservationForFullCalendar(MeetingRoom $meetingRoom)
    {
        $allMeetingRoomReservation = [];

        // On récupère les RDV de la salle selectionné
        $meetingRoomReservations = $this->meetingRoomReservationRepository->findBy(['meetingRoom' => $meetingRoom]);
        foreach ($meetingRoomReservations as $m) {
            $allMeetingRoomReservation[] = [
                'id' => $m->getId(),
                'start' => $m->getStartAt()->format('Y-m-d H:i:s'),
                'end' => $m->getEndAt()->format('Y-m-d H:i:s'),
                'title' => $m->getTitle(),
                'description' => $m->getDescription(),
                'backgroundColor' => '#0053a3',
                'borderColor' => '#0053a3',
            ];
        }

        return $allMeetingRoomReservation;
    }

    #[Route('/reservation/meeting_room', name: 'app_meeting_room')]
    public function mettingRoom(Request $request, EntityManagerInterface $entityManager): Response
    {

        // Création de nos deux formulaires
        $chooseMeetingRoomForm = $this->createForm(ChooseMeetingRoomFormType::class);
        $chooseMeetingRoomForm->handleRequest($request);

        $meetingRoomReservation = new MeetingRoomReservation();
        // We set date to today as default
        $meetingRoomReservation->setStartAt(new \DateTime('now'));
        $meetingRoomReservation->setEndAt(new \DateTime('now'));
        $meetingRoomReservationForm = $this->createForm(MeetingRoomReservationType::class, $meetingRoomReservation);

        $meetingRoomReservationForm->handleRequest($request);

        $jsonifyMeetingRoomReservation = [];
        // Formulaire pour afficher les créneaux horaires des salles
        if ($chooseMeetingRoomForm->isSubmitted() && $chooseMeetingRoomForm->isValid()) {

            // Si on à une salle de réunion sélectionné
            $meetingRoom = $chooseMeetingRoomForm->get('meetingRoom')->getData();
            if ($meetingRoom) {
                // On récupère les RDV de la salle selectionné
                $jsonifyMeetingRoomReservation = $this->getReservationForFullCalendar($meetingRoom);
            }
        }

        // Formulaire de réservation d'une salle de réunion
        if ($meetingRoomReservationForm->isSubmitted() && $meetingRoomReservationForm->isValid()) {

            // Si on à une salle de réunion sélectionné
            if ($meetingRoomReservation->getMeetingRoom()) {
                // On définit l'utilisateur
                $meetingRoomReservation->setUser($this->getUser());

                // on vérifie si une réservation n'existe pas déja
                $reservationExist = $this->meetingRoomReservationRepository->checkExistingReservation($meetingRoomReservation->getMeetingRoom(), $meetingRoomReservation->getStartAt(), $meetingRoomReservation->getEndAt());
                if (!$reservationExist) {
                    $entityManager->persist($meetingRoomReservation);
                    $entityManager->flush();

                    $this->addFlash('reservation_meeting_room_success', new translatableMessage('Votre réservation à bien été ajouté !'));
                } else {
                    $this->addFlash('reservation_meeting_room_error', new translatableMessage('Une réservation existe déjâ pour les horraires de la salle de réunion selectionné !'));
                }

                // On récupère les RDV de la salle selectionné
                $jsonifyMeetingRoomReservation = $this->getReservationForFullCalendar($meetingRoomReservation->getMeetingRoom());
            } else {
                $this->addFlash('reservation_meeting_room_error', new translatableMessage('Veuillez sélectionner une salle de réunion !'));
            }
        }

        return $this->render('reservation/meeting_room.html.twig', [
            'chooseMeetingRoom' => $chooseMeetingRoomForm->createView(),
            'meetingRoomReservationForm' => $meetingRoomReservationForm->createView(),
            'meetingRoomReservations' => json_encode($jsonifyMeetingRoomReservation),
        ]);
    }
}
