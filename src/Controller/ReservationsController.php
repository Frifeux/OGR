<?php

namespace App\Controller;

use App\Repository\EquipmentReservationRepository;
use App\Repository\MeetingRoomReservationRepository;
use App\Repository\OfficeReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationsController extends AbstractController
{
    private EquipmentReservationRepository $equipmentReservationRepository;
    private MeetingRoomReservationRepository $meetingRoomReservationRepository;
    private OfficeReservationRepository $officeReservationRepository;

    public function __construct(OfficeReservationRepository $officeReservationRepository, EquipmentReservationRepository $equipmentReservationRepository, MeetingRoomReservationRepository $meetingRoomReservationRepository)
    {
        $this->officeReservationRepository = $officeReservationRepository;
        $this->equipmentReservationRepository = $equipmentReservationRepository;
        $this->meetingRoomReservationRepository = $meetingRoomReservationRepository;
    }

    // TODO: TROUVER UN NOM DE ROUTE POUR CETTE ACTION
    #[Route('/reservation/manage', name: 'app_reservation')]
    public function index(): Response
    {
        // retrive all reservations from user
        $officeReservations = $this->officeReservationRepository->findBy(['user' => $this->getUser()]);
        $equipmentReservations = $this->equipmentReservationRepository->findBy(['user' => $this->getUser()]);
        $meetingRoomReservations = $this->meetingRoomReservationRepository->findBy(['user' => $this->getUser()]);

        return $this->render('reservation/reservations.html.twig', [
            'officeReservations' => $officeReservations,
            'equipmentReservations' => $equipmentReservations,
            'meetingRoomReservations' => $meetingRoomReservations,
        ]);
    }
}
