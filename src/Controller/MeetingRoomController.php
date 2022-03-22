<?php

namespace App\Controller;

use App\Entity\MeetingRoomReservation;
use App\Form\ChooseMeetingRoomFormType;
use App\Form\MeetingRoomReservationType;
use App\Repository\MeetingRoomRepository;
use App\Repository\MeetingRoomReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Translation\TranslatableMessage;

class MeetingRoomController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/reservation/metting_room', name: 'metting_room')]
    public function mettingRoom(Request $request, MeetingRoomReservationRepository $meetingRoomReservationRepository, MeetingRoomRepository $meetingRoomRepository, EntityManagerInterface $entityManager): Response
    {

        // Création de nos deux formulaires
        $chooseMeetingRoomForm = $this->createForm(ChooseMeetingRoomFormType::class);
        $chooseMeetingRoomForm->handleRequest($request);

        $meetingRoomReservation = new MeetingRoomReservation();
        $meetingRoomReservationForm = $this->createForm(MeetingRoomReservationType::class, $meetingRoomReservation);
        $meetingRoomReservationForm->handleRequest($request);

        $jsonifyMeetingRoomReservation = [];
        // Formulaire pour afficher les créneaux horaires des salles
        if ($chooseMeetingRoomForm->isSubmitted() && $chooseMeetingRoomForm->isValid()) {

            $meetingRoom = $meetingRoomRepository->findOneBy(['id' => $chooseMeetingRoomForm->get('meetingRoom')->getData()]);
            $meetingRoomReservation->setMeetingRoom($meetingRoom);

            // On récupère les RDV de la salle selectionné
            $meetingRoomReservations = $meetingRoomReservationRepository->findBy(['meetingRoom' => $chooseMeetingRoomForm->get('meetingRoom')->getData()]);
            foreach ($meetingRoomReservations as $m) {
                $jsonifyMeetingRoomReservation[] = [
                    'id' => $m->getId(),
                    'start' => $m->getStartAt()->format('Y-m-d H:i:s'),
                    'end' => $m->getEndAt()->format('Y-m-d H:i:s'),
                    'title' => $m->getTitle(),
                    'description' => $m->getDescription(),
                    'backgroundColor' => '#0053a3',
                    'borderColor' => '#0053a3',
                ];
            }
        }

        // Formulaire de réservation d'une salle de réunion
        if ($meetingRoomReservationForm->isSubmitted() && $meetingRoomReservationForm->isValid()) {

            $meetingRoom = $meetingRoomRepository->findOneBy(['id' => $meetingRoomReservationForm->get('meetingRoom')->getData()]);
            if ($meetingRoom)
            {
                // On définit les attributs de notre objet MeetingRoomReservation
                $meetingRoomReservation->setMeetingRoom($meetingRoom);
                $meetingRoomReservation->setUser($this->security->getUser());

                // on vérifie si une réservation n'existe pas déja
                $reservationExist = $meetingRoomReservationRepository->checkExistingReservation($meetingRoom->getId(), $meetingRoomReservation->getStartAt(), $meetingRoomReservation->getEndAt());
                if (!$reservationExist){
                    $entityManager->persist($meetingRoomReservation);
                    $entityManager->flush();

                    $this->addFlash('reservation_meeting_room_success', new translatableMessage('Votre réservation à bien été ajouté !'));
                }else{
                    $this->addFlash('reservation_meeting_room_error', new translatableMessage('Une réservation existe déjâ pour les horraires de la salle de réunion selectionné !'));
                }
            }
        }

        return $this->render('reservation/meeting_room.html.twig', [
            'chooseMeetingRoom' => $chooseMeetingRoomForm->createView(),
            'meetingRoomReservationForm' => $meetingRoomReservationForm->createView(),
            'meetingRoomReservations' => json_encode($jsonifyMeetingRoomReservation),
        ]);
    }

}
