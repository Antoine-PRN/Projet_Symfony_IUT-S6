<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EventRepository $eventRepository, Request $request): Response
    {
        // Vérifier si la route actuelle est '/'
        if ($request->attributes->get('_route') === 'home') {
            // Récupérer les trois événements dont la date est la plus proche et future
            $events = $eventRepository->findUpcomingEvents(3); // 3 événements max

            return $this->render('home/index.html.twig', [
                'events' => $events,
            ]);
        }

        return $this->render('home/index.html.twig');
    }
}
