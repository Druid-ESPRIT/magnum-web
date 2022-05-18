<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventBackController extends AbstractController
{
    /**
     * @Route("/event/back", name="app_event_back")
     * @param EventRepository $eventRepository
     * @param UsersRepository $userRepository
     * @return Response
     */
    public function index(EventRepository $eventRepository,UsersRepository $userRepository): Response
    {
        $user = $this->getUser();

        $events = $eventRepository->findAll();

        return $this->render('back/event/home.html.twig', [
            'events' => $events,
        ]);
    }
}
