<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeetingRoomController extends AbstractController
{
    #[Route('/reservation/metting_room', name: 'metting_room')]
    public function mettingRoom(): Response
    {
        return $this->render('reservation/metting_room.html.twig');
    }
}
