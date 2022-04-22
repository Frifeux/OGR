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
    private EntityManagerInterface $entityManager;

    public function __construct(OfficeReservationRepository      $officeReservationRepository,
                                EquipmentReservationRepository   $equipmentReservationRepository,
                                MeetingRoomReservationRepository $meetingRoomReservationRepository,
                                EntityManagerInterface           $entityManager)
    {
        $this->officeReservationRepository = $officeReservationRepository;
        $this->equipmentReservationRepository = $equipmentReservationRepository;
        $this->meetingRoomReservationRepository = $meetingRoomReservationRepository;
        $this->entityManager = $entityManager;
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

    // Delete a reservation from user
    #[Route('/reservation/delete/{id}', name: 'app_reservation_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $officeReservation = $this->officeReservationRepository->findOneBy(['user' => $this->getUser(), 'id' => $id]);
        $equipmentReservation = $this->equipmentReservationRepository->findOneBy(['user' => $this->getUser(), 'id' => $id]);
        $meetingRoomReservation = $this->meetingRoomReservationRepository->findOneBy(['user' => $this->getUser(), 'id' => $id]);

        if ($officeReservation) {
            $this->entityManager->remove($officeReservation);
        } elseif ($equipmentReservation) {
            $this->entityManager->remove($equipmentReservation);
        } elseif ($meetingRoomReservation) {
            $this->entityManager->remove($meetingRoomReservation);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_reservation');
    }
}
