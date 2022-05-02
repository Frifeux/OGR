<?php

namespace App\Controller;

use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use App\Form\MeetingRoomReservationType;
use App\Repository\MeetingRoomRepository;
use App\Repository\MeetingRoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class MeetingRoomController extends AbstractController
{

    private MeetingRoomReservationRepository $meetingRoomReservationRepository;
    private MeetingRoomRepository $meetingRoomRepository;

    public function __construct(MeetingRoomReservationRepository $meetingRoomReservationRepository, MeetingRoomRepository $meetingRoomRepository)
    {
        $this->meetingRoomReservationRepository = $meetingRoomReservationRepository;
        $this->meetingRoomRepository = $meetingRoomRepository;
    }

    // Transform meeting room object in json format for the calendar
    public function getReservationForFullCalendar(MeetingRoom $meetingRoom, \DateTime $startDate, \DateTime $endDate): array
    {
        $allMeetingRoomReservation = [];

        // On récupère les RDV de la salle selectionné
        $meetingRoomReservations = $this->meetingRoomReservationRepository->findReservationsForADateRange($meetingRoom, $startDate, $endDate);
        foreach ($meetingRoomReservations as $m) {
            $allMeetingRoomReservation[] = [
                'title' => $m->getTitle(),
                'description' => $m->getDescription(),
                'start' => $m->getStartAt()->format('Y-m-d H:i:s'),
                'end' => $m->getEndAt()->format('Y-m-d H:i:s'),
                'backgroundColor' => '#0053a3',
                'borderColor' => '#0053a3',
            ];
        }

        return $allMeetingRoomReservation;
    }

    // We check if it's the weekend
    public function isWeekend(\DateTime $date): ?bool
    {
        return (date('N', $date->getTimestamp()) >= 6);
    }

    #[Route('/meeting-room', name: 'app_meeting_room')]
    public function mettingRoom(Request $request, EntityManagerInterface $entityManager): Response
    {

        $meetingRoomReservation = new MeetingRoomReservation();
        // We set date to today as default
        $meetingRoomReservation->setStartAt(new \DateTime('now'));
        $meetingRoomReservation->setEndAt(new \DateTime('now'));
        $meetingRoomReservationForm = $this->createForm(MeetingRoomReservationType::class, $meetingRoomReservation);

        $meetingRoomReservationForm->handleRequest($request);

        // Formulaire de réservation d'une salle de réunion
        if ($meetingRoomReservationForm->isSubmitted() && $meetingRoomReservationForm->isValid()) {

            // Si on à une salle de réunion sélectionné
            if ($meetingRoomReservation->getMeetingRoom()) {
                // On définit l'utilisateur
                $meetingRoomReservation->setUser($this->getUser());

                // On verifie que la reservation n'est pas le weekend
                if ($this->isWeekend($meetingRoomReservation->getStartAt()) || $this->isWeekend($meetingRoomReservation->getEndAt())) {
                    $this->addFlash('reservation_meeting_room_error', new TranslatableMessage('Vous ne pouvez pas réserver un créneau le weekend'));
                } else {
                    // on vérifie si une réservation n'existe pas déja
                    $reservationExist = $this->meetingRoomReservationRepository->checkExistingReservation($meetingRoomReservation->getMeetingRoom(), $meetingRoomReservation->getStartAt(), $meetingRoomReservation->getEndAt());
                    if (!$reservationExist) {
                        $entityManager->persist($meetingRoomReservation);
                        $entityManager->flush();

                        $this->addFlash('reservation_meeting_room_success', new TranslatableMessage('Votre réservation a bien été ajoutée !'));
                    } else {
                        $this->addFlash('reservation_meeting_room_error', new TranslatableMessage('Une réservation existe déjà pour les horaires de la salle de réunion sélectionnée !'));
                    }
                }
            } else {
                $this->addFlash('reservation_meeting_room_error', new TranslatableMessage('Veuillez sélectionner une salle de réunion !'));
            }
        }

        return $this->render('meeting_room/index.html.twig', [
            'meetingRoomReservationForm' => $meetingRoomReservationForm->createView(),
        ]);
    }

    // A route that getting all reservation for a meeting room and return a json for fullcalendar
    #[Route('/meeting-room/reservation/{id}', name: 'app_meeting_room_reservation', methods: ['GET'])]
    public function getReservationForMeetingRoom(int $id, Request $request): JsonResponse
    {
        //get startDate and endDate from request
        $startDate = new \DateTime($request->get('startDate'));
        $endDate = new \DateTime($request->get('endDate'));

        $allMeetingRoomReservation = [];
        $meetingRoom = $this->meetingRoomRepository->findOneBy(['id' => $id]);
        if ($meetingRoom) {

            $allMeetingRoomReservation =  $this->getReservationForFullCalendar($meetingRoom, $startDate, $endDate);
        }

        return new JsonResponse($allMeetingRoomReservation);
    }

}
