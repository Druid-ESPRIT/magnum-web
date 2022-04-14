<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Review;
use App\Form\EventType;
use App\Form\ReviewType;
use App\Repository\EventRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="app_event_index", methods={"GET"})
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_event_new", methods={"GET", "POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function new(Request $request, EventRepository $eventRepository,UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('Image')->getData();

            if($imageFile)
            {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads'),
                        $newFilename
                    );
                    $event->setImage($newFilename);
                    $event->setStatus('Not Finished');
                    $event->setUser($user);
                } catch (FileException $e) {
                }
                $eventRepository->add($event);
            }


            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_event_delete", methods={"GET", "POST"})
     * @param Request $request
     * @param Event $event
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {

            $eventRepository->remove($event);


        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_event_show", methods={"GET", "POST"})
     * @param Request $request
     * @param Event $event
     * @param ReviewRepository $reviewRepository
     * @param UserRepository $userRepository
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function show(Request $request, Event $event,ReviewRepository $reviewRepository,UserRepository $userRepository,ValidatorInterface $validator): Response
    {
        $user = $userRepository->find(1);
        $reviews = $reviewRepository->findBy(["user"=>$user,"Event"=>$event]);


        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setEvent($event);
            $review->setUser($user);
            $errors = $validator->validate($review);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                return $this->render('event/show.html.twig', [
                    'errors'=> $errorsString,
                    'event' => $event,
                    'form'=>$form->createView(),
                    'reviews'=>$reviews
                ]);
            }else
            {
                $reviewRepository->add($review);
                return $this->render('event/show.html.twig', [
                    'errors'=> '',
                    'event' => $event,
                    'form'=>$form->createView(),
                    'reviews'=>$reviews
                ]);
            }

        }


        return $this->render('event/show.html.twig', [
            'errors'=> '',
            'event' => $event,
            'form'=>$form->createView(),
            'reviews'=>$reviews
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_event_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Event $event
     * @param EventRepository $eventRepository
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function edit(Request $request, Event $event, EventRepository $eventRepository,ValidatorInterface $validator): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $errors = $validator->validate($event);


        if ($form->isSubmitted() && $form->isValid()) {

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                return $this->render('event/edit.html.twig', [
                    'errors'=> $errorsString,
                    'event' => $event,
                    'form' => $form->createView(),
                ]);
            }else{
                $eventRepository->add($event);
                return $this->redirectToRoute('app_event_show', ['id'=>$event->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('event/edit.html.twig', [
            'errors'=>'',
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }


}
