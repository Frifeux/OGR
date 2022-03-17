<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use App\Entity\EquipmentReservation;
use App\Entity\MeetingRoom;
use App\Entity\MeetingRoomReservation;
use App\Entity\Office;
use App\Entity\OfficeReservation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-home.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('OGR');
    }

    public function configureMenuItems(): iterable
    {

//        Lien pour trouver les icons
//        https://fontawesome.com/v5/search

//        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::linkToRoute(new TranslatableMessage('Menu Principal'), 'fa fa-sign-out', 'app_home');

        // Section: Gestion des utilisateurs
        yield MenuItem::section(new TranslatableMessage('Gestion des utilisateurs'));
        yield MenuItem::linkToCrud(new TranslatableMessage('Utilisateurs'), 'fa fa-user', User::class);

        yield MenuItem::section(new TranslatableMessage('Gestion des objects'));
        yield MenuItem::linkToCrud(new TranslatableMessage('Salles de réunion'), 'fa fa-camera', MeetingRoom::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Bureaux'), 'fa fa-door-open', Office::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Matériel'), 'fa fa-desktop', Equipment::class);

        yield MenuItem::section(new TranslatableMessage('Gestion des réservations'));
        yield MenuItem::linkToCrud(new TranslatableMessage('Salles de réunion'), 'fa fa-calendar', MeetingRoomReservation::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Bureaux'), 'fa fa-calendar', OfficeReservation::class);
        yield MenuItem::linkToCrud(new TranslatableMessage('Matériel'), 'fa fa-calendar', EquipmentReservation::class);
    }
}
