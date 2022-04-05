<?php

namespace App\Controller;

use App\Entity\OfficeReservation;
use App\Form\ChooseMeetingRoomFormType;
use App\Form\ChooseOfficeReservationFormType;
use App\Repository\OfficeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class OfficeController extends AbstractController
{

    public function __construct(OfficeRepository $officeRepository, EntityManagerInterface $entityManager)
    {
        $this->officeRepository = $officeRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/reservation/office', name: 'app_office')]
    public function index(Request $request): Response
    {

        $availableReservations = [];

        $officeReservation = new OfficeReservation();

        // creation of the form to get available office reservations
        $chooseOfficeReservationFormType = $this->createForm(ChooseOfficeReservationFormType::class, $officeReservation);
        $chooseOfficeReservationFormType->handleRequest($request);

        if ($chooseOfficeReservationFormType->isSubmitted() && $chooseOfficeReservationFormType->isValid()) {

            $availableReservations = $this->officeRepository->findAll();
        }


        return $this->render('reservation/office.html.twig', [
            'chooseOfficeReservationFormType' => $chooseOfficeReservationFormType->createView(),
            'availableOfficeReservations' => $availableReservations,
            'officeReservation' => $officeReservation,
        ]);
    }

    #[Route('/reservation/office/{id}-{startAt}-{endAt}', name: 'app_office_reservation', methods: ['GET']), ]
    public function addReservation(int $id, \DateTime $startAt, \DateTime $endAt): Response
    {

        $office = $this->officeRepository->find($id);

        if ($office) {

            $officeReservation = new OfficeReservation();
            $officeReservation->setOffice($office);
            $officeReservation->setStartAt($startAt);
            $officeReservation->setEndAt($endAt);

            $officeReservation->setUser($this->getUser());

            $this->entityManager->persist($officeReservation);
            $this->entityManager->flush();

            $this->addFlash('success', new TranslatableMessage('reservation.office.success', [], 'messages'));

        }else{
            $this->addFlash('reservation_office_error', new TranslatableMessage('Impossible de trouver le bureau que vous avez demandé'));
        }

        return $this->redirectToRoute('app_office');
    }
}
