<?php

namespace App\Controller;

use App\Entity\OfficeReservation;
use App\Form\ChooseOfficeReservationFormType;
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

class OfficeController extends AbstractController
{
    private OfficeRepository $officeRepository;
    private EntityManagerInterface $entityManager;
    private OfficeReservationRepository $officeReservationRepository;
    private TranslatorInterface $translator;

    public function __construct(OfficeRepository $officeRepository,
                                OfficeReservationRepository $officeReservationRepository,
                                EntityManagerInterface $entityManager,
                                TranslatorInterface $translator)
    {
        $this->officeRepository = $officeRepository;
        $this->entityManager = $entityManager;
        $this->officeReservationRepository = $officeReservationRepository;
        $this->translator = $translator;
    }

    #[Route('/office', name: 'app_office')]
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
            $availableReservations = $this->officeRepository->search(
                $officeReservation->getStartAt(),
                $officeReservation->getEndAt(),
                $chooseOfficeReservationForm->get('location')->getData(),
                $chooseOfficeReservationForm->get('floor')->getData(),
                $chooseOfficeReservationForm->get('department')->getData(),
            );
        }

        return $this->render('office/index.html.twig', [
            'chooseOfficeReservationForm' => $chooseOfficeReservationForm->createView(),
            'availableOfficeReservations' => $availableReservations,
            'officeReservation' => $officeReservation,
        ]);
    }

    #[Route('/office/add/{id}', name: 'app_office_add_reservation', methods: ['POST'])]
    public function addReservation(int $id, Request $request): JsonResponse
    {
        // get the data send by the post request
        $data = $request->request->all();

        $startAt = new \DateTime($data['startAt']);
        $endAt = new \DateTime($data['endAt']);

        // we get the office we want to reserve
        $office = $this->officeRepository->find($id);
        if ($office && $startAt < $endAt) {

            // We verify if the office is already reserved to be sure that the user can't reserve it twice
            // that he hasn't modified the link by himself to set bad dates
            $officeReservation = $this->officeReservationRepository->findBy(['office' => $office, 'startAt' => $startAt, 'endAt' => $endAt]);

            if (count($officeReservation) > 0) {

                // return a json response with the error message translated for the current locale of the user
                return new JsonResponse(['error' => $this->translator->trans("Il n\'y a pas de bureau disponible à cette date")]);
            }

            $officeReservation = new OfficeReservation();
            $officeReservation->setOffice($office);
            $officeReservation->setStartAt($startAt);
            $officeReservation->setEndAt($endAt);

            $officeReservation->setUser($this->getUser());

            $this->entityManager->persist($officeReservation);
            $this->entityManager->flush();

            return new JsonResponse(['success' => $this->translator->trans('Votre réservation a bien été ajoutée !')]);
        }

        return new JsonResponse(['error' => $this->translator->trans('Impossible de trouver le bureau que vous avez demandé')]);
    }
}
