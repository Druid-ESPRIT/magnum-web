<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventBackController extends AbstractController
{
    /**
     * @Route("/event/back", name="app_event_back")
     * @param EventRepository $eventRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        $events = $eventRepository->findBy(["User"=>$user]);

        return $this->render('back/event/home.html.twig', [
            'events' => $events,
        ]);
    }
}
