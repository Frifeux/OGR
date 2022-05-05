<?php

namespace App\Controller;

use App\Repository\EquipmentReservationRepository;
use App\Repository\MeetingRoomReservationRepository;
use App\Repository\OfficeReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationsController extends AbstractController
{
    private EquipmentReservationRepository $equipmentReservationRepository;
    private MeetingRoomReservationRepository $meetingRoomReservationRepository;
    private OfficeReservationRepository $officeReservationRepository;

    public function __construct(OfficeReservationRepository      $officeReservationRepository,
                                EquipmentReservationRepository   $equipmentReservationRepository,
                                MeetingRoomReservationRepository $meetingRoomReservationRepository)
    {
        $this->officeReservationRepository = $officeReservationRepository;
        $this->equipmentReservationRepository = $equipmentReservationRepository;
        $this->meetingRoomReservationRepository = $meetingRoomReservationRepository;
    }

    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        // retrieve all reservations from user and order them by date
        $officeReservations = $this->officeReservationRepository->findBy(['user' => $this->getUser()], ['startAt' => 'DESC', 'endAt' => 'DESC']);
        $equipmentReservations = $this->equipmentReservationRepository->findBy(['user' => $this->getUser()], ['startAt' => 'DESC', 'endAt' => 'DESC']);
        $meetingRoomReservations = $this->meetingRoomReservationRepository->findBy(['user' => $this->getUser()], ['startAt' => 'DESC', 'endAt' => 'DESC']);

        return $this->render('reservation/index.html.twig', [
            'officeReservations' => $officeReservations,
            'equipmentReservations' => $equipmentReservations,
            'meetingRoomReservations' => $meetingRoomReservations,
        ]);
    }
}
