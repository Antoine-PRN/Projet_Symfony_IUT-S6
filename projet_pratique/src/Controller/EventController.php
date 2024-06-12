<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class EventController extends AbstractController
{
    private $entityManager;
    private $security;
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, Security $security)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/event/new', name: 'event_new')]
    public function create(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur connecté
            $user = $this->security->getUser();
            if ($user) {
                $event->setCreator($user);
            } else {
                // Gérer le cas où l'utilisateur n'est pas connecté
                // Par exemple, en renvoyant une erreur ou en redirigeant vers la page de connexion
                throw new \Exception('L\'utilisateur doit être connecté pour créer un événement.');
            }

            $this->entityManager->persist($event);
            $this->entityManager->flush();

            return $this->redirectToRoute('event_list');
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
