<?php

namespace App\Controller;

use App\Entity\OfficeReservation;
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
    private OfficeRepository $officeRepository;
    private EntityManagerInterface $entityManager;

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
        // We set date to today as default
        $officeReservation->setStartAt(new \DateTime('now'));
        $officeReservation->setEndAt(new \DateTime('now'));

        // creation of the form to get available office reservations
        $chooseOfficeReservationForm = $this->createForm(ChooseOfficeReservationFormType::class, $officeReservation);
        $chooseOfficeReservationForm->handleRequest($request);

        if ($chooseOfficeReservationForm->isSubmitted() && $chooseOfficeReservationForm->isValid()) {

            /* It is used to get offices with some criteria. */
            $availableReservations = $this->officeRepository->searchOffice(
                $officeReservation->getStartAt(),
                $officeReservation->getEndAt(),
                $chooseOfficeReservationForm->get('location')->getData(),
                $chooseOfficeReservationForm->get('floor')->getData(),
                $chooseOfficeReservationForm->get('department')->getData(),
            );

        }

        return $this->render('reservation/office.html.twig', [
            'chooseOfficeReservationForm' => $chooseOfficeReservationForm->createView(),
            'availableOfficeReservations' => $availableReservations,
            'officeReservation' => $officeReservation,
        ]);
    }

    #[Route('/reservation/office/{id}-{startAt}-{endAt}', name: 'app_office_add_reservation', requirements: ['id' => '\d+', 'startAt' => '\d{10}', 'endAt' => '\d{10}'], methods: ['GET'])]
    public function addReservation(int $id, \DateTime $startAt, \DateTime $endAt): Response
    {
        $office = $this->officeRepository->find($id);
        if ($office && $startAt < $endAt) {

            $officeReservation = new OfficeReservation();
            $officeReservation->setOffice($office);
            $officeReservation->setStartAt($startAt);
            $officeReservation->setEndAt($endAt);

            $officeReservation->setUser($this->getUser());

            $this->entityManager->persist($officeReservation);
            $this->entityManager->flush();

            $this->addFlash('reservation_office_success', new TranslatableMessage('Votre réservation à bien été ajouté !'));
        } else {
            $this->addFlash('reservation_office_error', new TranslatableMessage('Impossible de trouver le bureau que vous avez demandé'));
        }

        return $this->redirectToRoute('app_office');
    }
}
