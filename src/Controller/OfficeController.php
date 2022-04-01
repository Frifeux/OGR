<?php

namespace App\Controller;

use App\Form\ChooseMeetingRoomFormType;
use App\Form\ChooseOfficeReservationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfficeController extends AbstractController
{
    #[Route('/reservation/office', name: 'app_office')]
    public function office(Request $request): Response
    {

        // Création de nos deux formulaires
        $chooseOfficeReservationFormType = $this->createForm(ChooseOfficeReservationFormType::class);
        $chooseOfficeReservationFormType->handleRequest($request);


        return $this->render('reservation/office.html.twig', [
            'chooseOfficeReservationFormType' => $chooseOfficeReservationFormType->createView(),
        ]);
    }
}
