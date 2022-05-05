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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

class EquipmentController extends AbstractController
{
    private EquipmentRepository $equipmentRepository;
    private EntityManagerInterface $entityManager;
    private EquipmentReservationRepository $equipmentReservationRepository;
    private TranslatorInterface $translator;

    public function __construct(EquipmentRepository $equipmentRepository,
                                EquipmentReservationRepository $equipmentReservationRepository,
                                EntityManagerInterface $entityManager,
                                TranslatorInterface $translator)
    {
        $this->equipmentRepository = $equipmentRepository;
        $this->entityManager = $entityManager;
        $this->equipmentReservationRepository = $equipmentReservationRepository;
        $this->translator = $translator;
    }

    #[Route('/equipment', name: 'app_equipment')]
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

        return $this->render('equipment/index.html.twig', [
            'chooseEquipmentReservationForm' => $chooseEquipmentReservationFormType->createView(),
            'availableEquipmentReservations' => $availableReservations,
            'equipmentReservation' => $equipmentReservation,
        ]);
    }

    #[Route('/equipment/add/{id}', name: 'app_equipment_add_reservation', methods: ['POST'])]
    public function addReservation(int $id, Request $request): JsonResponse
    {
        // get the data send by the post request
        $data = $request->request->all();

        $startAt = new \DateTime($data['startAt']);
        $endAt = new \DateTime($data['endAt']);

        $equipment = $this->equipmentRepository->find($id);
        if ($equipment && $startAt < $endAt) {

            // We verify if the equipment is already reserved to be sure that the user can't reserve it twice
            // that he hasn't modified the link by himself to set bad dates
            $equipmentReservation = $this->equipmentReservationRepository->findBy(['equipment' => $equipment, 'startAt' => $startAt, 'endAt' => $endAt]);
            if (count($equipmentReservation) > 0) {

                // return a json response with the error message translated for the current locale of the user
                return new JsonResponse(['error' => $this->translator->trans("Il n\'y a pas de matériel disponible à cette date")]);
            }

            $equipmentReservation = new EquipmentReservation();
            $equipmentReservation->setEquipment($equipment);
            $equipmentReservation->setStartAt($startAt);
            $equipmentReservation->setEndAt($endAt);

            $equipmentReservation->setUser($this->getUser());

            $this->entityManager->persist($equipmentReservation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => $this->translator->trans('Votre réservation a bien été ajoutée !')]);
        }

        return new JsonResponse(['error' => $this->translator->trans('Impossible de trouver l\'équipement que vous avez demandé')]);
    }

    // Delete a reservation from user
    #[Route('/equipment/delete/{id}', name: 'app_equipment_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        // we get the meeting room we want to delete
        $equipmentReservation = $this->equipmentReservationRepository->findOneBy(['user' => $this->getUser(), 'id' => $id]);

        // if the meeting room exists we delete it
        if ($equipmentReservation) {
            $this->entityManager->remove($equipmentReservation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => $this->translator->trans('Le matériel à bien été supprimé')]);
        }
        return new JsonResponse(['error' => $this->translator->trans('Impossible de supprimé le matériel, il n\'existe pas !')]);
    }
}
