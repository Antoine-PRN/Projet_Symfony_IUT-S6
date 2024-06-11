<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\Persistence\ManagerRegistry;

class EventController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/event/new', name: 'event_new')]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assigner le créateur (utilisateur connecté)
            $user = $this->getUser();
            $event->setCreator($user);

            // Sauvegarder l'événement dans la base de données
            $entityManager = $doctrine->getManager();
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/events', name: 'event_list')]
    public function list(): Response
    {
        $events = $this->doctrine
            ->getRepository(Event::class)
            ->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }
}
