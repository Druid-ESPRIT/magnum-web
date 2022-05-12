<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Review;
use App\Entity\User;
use App\Form\EventType;
use App\Form\ReviewType;
use App\Repository\EventRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
        $events = $eventRepository->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/wallet",name="app_event_wallet")
     * @param EventRepository $eventRepository
     * @param UsersRepository $userRepository
     * @return Response
     */
    public function walletIndex(EventRepository $eventRepository, UsersRepository $userRepository)
    {
        $events = $eventRepository->findAll();
        $total = 0;
        $totalP =0;

        foreach ($events as $event)
        {
            $total+=($event->getPrix()*$event->getParticipants()->count());
            $totalP+=$event->getParticipants()->count();
        }

        return $this->render('event/wallet.html.twig', [
            'events' => $events,
            'total'=>$total,
            'totalP'=>$totalP
        ]);
    }


    /**
     * @Route("/new", name="app_event_new", methods={"GET", "POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param UsersRepository $userRepository
     * @return Response
     */
    public function new(Request $request, EventRepository $eventRepository,UsersRepository $userRepository): Response
    {
        $user = $this->getUser();
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
     * @param EventRepository $eventRepository
     * @param ReviewRepository $reviewRepository
     * @param UsersRepository $userRepository
     * @param ValidatorInterface $validator
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function show(Request $request, Event $event,EventRepository $eventRepository, ReviewRepository $reviewRepository,UsersRepository $userRepository,ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        $reviews = $reviewRepository->findBy(["Event"=>$event]);


        $participants = $event->getParticipants();

        /* Event is Full */
        $isFull = $event->getMaxParticipants() == $participants->count();
        /* ---------------- */


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
                    'isFull'=> $isFull,
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
                    'isFull'=> $isFull,
                    'form'=>$form->createView(),
                    'reviews'=>$reviews
                ]);
            }

        }

        if($participants->contains($user))
        {
            return $this->render('event/show.html.twig', [
                'errors'=> '',
                'participe'=>false,
                'isFull'=> $isFull,
                'event' => $event,
                'form'=>$form->createView(),
                'reviews'=>$reviews
            ]);
        }
        else{
            return $this->render('event/show.html.twig', [
                'errors'=> '',
                'participe'=>true,
                'isFull'=> $isFull,
                'event' => $event,
                'form'=>$form->createView(),
                'reviews'=>$reviews
            ]);
        }

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

            var_dump( (string) $errors);
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

    /**
     * @param MailerInterface $mailer
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param ReviewRepository $reviewRepository
     * @param ValidatorInterface $validator
     * @param Event $event
     * @param UsersRepository $userRepository
     * @return Response
     * @throws TransportExceptionInterface
     * @Route("/{id}/participate",name="app_event_participate",methods={"GET","POST"})
     */
    public function UserParticipate(MailerInterface $mailer,Request $request,EventRepository $eventRepository,ReviewRepository $reviewRepository, ValidatorInterface $validator, Event $event,UsersRepository $userRepository)
    {
        $user = $this->getUser();
        $participants = $event->getParticipants();


        if($participants->contains($user))
        {
            return $this->redirectToRoute('app_event_show', ['id'=>$event->getId()], Response::HTTP_SEE_OTHER);
        }else{

            if($event->getPayant())
            {
                return  $this->redirectToRoute('checkout_event',['event'=>$event->getId()], Response::HTTP_SEE_OTHER);
            }else{
                /* QR CODE */
                $webPath = $this->getParameter('kernel.project_dir').'/public/';
                $result = Builder::create()
                    ->labelText($event->getName())
                    ->writerOptions([])
                    ->data("Event Name : ".$event->getName()." | Podcaster : ".$event->getUser()->getUsername()." | Event Date : ".$event->getDate()->format("d/M/Y")." | Event Location / Link : ".$event->getLocation()." | ")
                    ->logoPath($webPath.'/uploads/'.$event->getImage())
                    ->logoResizeToHeight(120)
                    ->logoResizeToWidth(120)
                    ->logoPunchoutBackground(false)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                    ->size(300)
                    ->margin(10)
                    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->labelFont(new NotoSans(20))
                    ->labelAlignment(new LabelAlignmentCenter())
                    ->build();

                $result->saveToFile($webPath.'/uploads/qrcodes/event.png');
                /* ---------------------------------------------------------------------- */
                /* Email */
                $email = (new TemplatedEmail())
                    ->from(new Address('devel.magnum@gmail.com', 'Magnum Mail'))
                    ->to($user->getEmail())
                    ->subject('Order Passed Successfully')
                    ->embedFromPath($webPath.'/uploads/qrcodes/event.png')
                    ->html("<h1> Hi !</h1>
                    <p>Thank you for your purchase</p>
                    <p>Your name : ".$user->getUsername()."</p>
                    <p>This is a Confirmation Email for participating in Event : ".$event->getName()."</p>
                    <p>Please Bring and Show this email as a recipient when attending the Event to confirm your Identity</p>
                    <p>You can also use the QrCode Attached to this email as your Ticket</p>
                    <p>Thank you for your Trust !</p>");
                //->htmlTemplate('commande/email.html.twig');

                $mailer->send($email);

                /* ---------------------------------------------------------------------- */
                  $event->addParticipant($user);
           $eventRepository->add($event);
           return $this->redirectToRoute('app_event_show', ['id'=>$event->getId()], Response::HTTP_SEE_OTHER);
            }

        }

    }

    /**
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param ReviewRepository $reviewRepository
     * @param ValidatorInterface $validator
     * @param Event $event
     * @param UsersRepository $userRepository
     * @return Response
     * @Route("/{id}/sparticipate",name="app_event_sparticipate",methods={"GET","POST"})
     */
    public function RemoveUserParticipate(Request $request,EventRepository $eventRepository,ReviewRepository $reviewRepository, ValidatorInterface $validator, Event $event,UsersRepository $userRepository)
    {
        $user = $this->getUser();
        $participants = $event->getParticipants();

        if($participants->contains($user))
        {
            $event->removeParticipant($user);
            $eventRepository->add($event);
            return $this->redirectToRoute('app_event_show', ['id'=>$event->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_event_show', ['id'=>$event->getId()], Response::HTTP_SEE_OTHER);


    }



}
