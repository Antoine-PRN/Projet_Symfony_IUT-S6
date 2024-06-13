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
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

    private function canEditEvent(Event $event): bool
    {
        // Implémentez la logique pour vérifier si l'utilisateur peut modifier l'événement.
        // Exemple simple : seul le créateur de l'événement peut le modifier.
        $user = $this->security->getUser();
        return $user && $event->getCreator() === $user;
    }

    private function canDeleteEvent(Event $event): bool
    {
        // Implémentez la logique pour vérifier si l'utilisateur peut supprimer l'événement.
        // Exemple simple : seul le créateur de l'événement peut le supprimer.
        $user = $this->security->getUser();
        return $user && $event->getCreator() === $user;
    }

    #[Route('/event/new', name: 'event_new')]
    public function create(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la date de l'événement
            $eventDate = $form->get('date')->getData();

            // Vérifier si la date est dans le futur
            $today = new \DateTime('now');
            if ($eventDate < $today) {
                $this->addFlash('error', 'La date de l\'événement doit être dans le futur.');
                return $this->render('event/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Récupérer l'utilisateur connecté
            $user = $this->security->getUser();
            if ($user) {
                $event->setCreator($user);
            } else {
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
    public function list(Request $request, EventRepository $eventRepository): Response
    {
        $user = $this->security->getUser();
        $isAuthenticated = $user !== null;

        $page = $request->query->getInt('page', 1); // Récupère le numéro de page depuis la requête, 1 par défaut
        $limit = 5; // Nombre d'événements par page

        $queryBuilder = $eventRepository->createQueryBuilder('e')
            ->where('e.isPublic = true');

        if ($isAuthenticated) {
            $queryBuilder
                ->orWhere('e.creator = :user')
                ->setParameter('user', $user);
        }

        // Compter le nombre total d'événements
        $totalEvents = (clone $queryBuilder)->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $maxPages = ceil($totalEvents / $limit);

        // Pagination
        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $events = $queryBuilder->getQuery()->getResult();

        return $this->render('event/list.html.twig', [
            'events' => $events,
            'currentPage' => $page,
            'maxPages' => $maxPages,
        ]);
    }

    #[Route('/event/{id}', name: 'event_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $event = $this->doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        return $this->render('event/details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/{id}/edit', name: 'event_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, Request $request): Response
    {
        $event = $this->doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        // Vérifier si l'utilisateur peut modifier l'événement
        if (!$this->canEditEvent($event)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cet événement.');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->doctrine->getManager()->flush();

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/event/{id}/delete', name: 'event_delete', requirements: ['id' => '\d+'])]
    public function delete(int $id): Response
    {
        $event = $this->doctrine->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('L\'événement n\'existe pas.');
        }

        // Vérifier si l'utilisateur peut supprimer l'événement
        if (!$this->canDeleteEvent($event)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de supprimer cet événement.');
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('event_list');
    }
}
