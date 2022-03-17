<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomPageController extends AbstractController
{
    #[Route('/reservation/room', name: 'room')]
    public function room(): Response
    {
        return $this->render('room/reservation_room.html.twig');
    }
}
