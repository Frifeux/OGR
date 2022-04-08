<?php

namespace App\Controller;

use App\Entity\EquipmentReservation;
use App\Entity\OfficeReservation;
use App\Form\ChooseEquipmentReservationFormType;
use App\Form\ChooseOfficeReservationFormType;
use App\Repository\EquipmentRepository;
use App\Repository\EquipmentReservationRepository;
use App\Repository\OfficeRepository;
use App\Repository\OfficeReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class EquipmentController extends AbstractController
{
    private EquipmentRepository $equipmentRepository;
    private EntityManagerInterface $entityManager;
    private EquipmentReservationRepository $equipmentReservationRepository;

    public function __construct(EquipmentRepository $equipmentRepository, EquipmentReservationRepository $equipmentReservationRepository, EntityManagerInterface $entityManager)
    {
        $this->equipmentRepository = $equipmentRepository;
        $this->entityManager = $entityManager;
        $this->equipmentReservationRepository = $equipmentReservationRepository;
    }

    #[Route('/reservation/equipment', name: 'app_equipment')]
    public function index(Request $request): Response
    {

        $availableReservations = [];
        $equipmentReservation = new EquipmentReservation();
        // We set date to today as default
        $equipmentReservation->setStartAt(new \DateTime('now'));
        $equipmentReservation->setEndAt(new \DateTime('now'));

        // creation of the form to get available office reservations
        $chooseEquipmentReservationFormType = $this->createForm(ChooseEquipmentReservationFormType::class, $equipmentReservation);
        $chooseEquipmentReservationFormType->handleRequest($request);

        if ($chooseEquipmentReservationFormType->isSubmitted() && $chooseEquipmentReservationFormType->isValid()) {

            /* It is used to get equipment with some criteria. */
            $availableReservations = $this->equipmentRepository->search(
                $equipmentReservation->getStartAt(),
                $equipmentReservation->getEndAt(),
                $chooseEquipmentReservationFormType->get('location')->getData(),
                $chooseEquipmentReservationFormType->get('type')->getData()
            );
        }

        return $this->render('reservation/equipment.html.twig', [
            'chooseEquipmentReservationForm' => $chooseEquipmentReservationFormType->createView(),
            'availableEquipmentReservations' => $availableReservations,
            'equipmentReservation' => $equipmentReservation,
        ]);
    }

    #[Route('/reservation/equipment/{id}-{startAt}-{endAt}', name: 'app_equipment_add_reservation', requirements: ['id' => '\d+', 'startAt' => '\d{10}', 'endAt' => '\d{10}'], methods: ['GET'])]
    public function addReservation(int $id, \DateTime $startAt, \DateTime $endAt): Response
    {
        $equipment = $this->equipmentRepository->find($id);
        if ($equipment && $startAt < $endAt) {

            // We verify if the equipment is already reserved to be sure that the user can't reserve it twice
            // that he hasn't modified the link by himself to set bad dates

            $equipmentReservation = $this->equipmentReservationRepository->findBy(['equipment' => $equipment, 'startAt' => $startAt, 'endAt' => $endAt]);
            if (count($equipmentReservation) > 0) {
                $this->addFlash('reservation_equipment_error', new TranslatableMessage('Il n\'y a pas de materiel disponible à cette date.'));
                return $this->redirectToRoute('app_equipment');
            }

            $equipmentReservation = new EquipmentReservation();
            $equipmentReservation->setEquipment($equipment);
            $equipmentReservation->setStartAt($startAt);
            $equipmentReservation->setEndAt($endAt);

            $equipmentReservation->setUser($this->getUser());

            $this->entityManager->persist($equipmentReservation);
            $this->entityManager->flush();

            $this->addFlash('reservation_equipment_success', new TranslatableMessage('Votre réservation à bien été ajouté !'));
        } else {
            $this->addFlash('reservation_equipment_error', new TranslatableMessage('Impossible de trouver le matériel que vous avez demandé'));
        }

        return $this->redirectToRoute('app_equipment');
    }
}
