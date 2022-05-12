<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\UsersRepository;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Order;
use App\Entity\Subscription;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\String\ByteString;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

class PaymentController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    /**
     * @Route("/payment", name="payment")
     */
    public function index(): Response
    {
        return $this->render('payment/index.html.twig');
    }


      /**
     * @Route("/checkout/{order}", name="checkout")
     */
    public function checkout(int $order): Response
    {
           Stripe::setApiKey('sk_test_51KUO54LURk8bHQH66NOA9MpsReXKIXeXkjRe76TuRYFEhjeWw4aFTG1OLaM0oYe62iZFrcGq4Q1kYDQ9ZjNVQeue00pExVMjwm');
            $repository=$this->getDoctrine()->getRepository(Order::class);
            $orders=$repository->find($order);
            $repository=$this->getDoctrine()->getRepository(Subscription::class);
            $subs=$repository->findOneBy(array('order' => $orders));
            $total=$orders->getTotal();
            $plan =$orders->getPlan();
            $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'Subsciption',
                        ],
                        'unit_amount'  => $total*100,
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', ["session_id" => $order], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
           
        ]);
    
         

        return $this->redirect($session->url, 303);
    }

     /**
     * @Route("/success-url", name="success_url")
     */
    public function successUrl(Request $request,MailerInterface $mailer): Response
    {
        $order = $request->query->get('session_id');
        $repository=$this->getDoctrine()->getRepository(Order::class);
            $orders=$repository->find($order);
            $repository=$this->getDoctrine()->getRepository(Subscription::class);
            $subs=$repository->findOneBy(array('order' => $orders));
            $total=$orders->getTotal();
            $plan =$orders->getPlan();
            $curr_user = $this->security->getUser();  
            $orders->setStatus("Completed");
            $subs->setStatus("Active");
             $em = $this->getDoctrine()->getManager();
             $em->persist($orders);
             $em->persist($subs);
             
             if ($plan >= 3 && $plan <= 5){
                $coupon = new Coupon(); 
                $coupon->setUserId($curr_user->getID());
                $coupon->setCode(ByteString::fromRandom(12)->toString());
                $coupon->setReduction(10);
                $coupon->setUsed("false");
                $coupon->setCreated(new \DateTime('NOW'));
                $em->persist($coupon);
                $email = (new TemplatedEmail())
                ->from('devel.magnum@gmail.com')
                ->to($curr_user->getEmail())
                ->subject('You\'ve recieved a new coupon')
                ->htmlTemplate('email/coupon.html.twig')
                ->context([
                  'coupon' => $coupon
                ]);
                $mailer->send($email);


             }
             else if ($plan >= 6 && $plan <= 12){
                $coupon = new Coupon(); 
                $coupon->setUserId($curr_user->getID());
                $coupon->setCode(ByteString::fromRandom(12)->toString());
                $coupon->setReduction(20);
                $coupon->setUsed("false");
                $coupon->setCreated(new \DateTime('NOW'));
                $em->persist($coupon);
                $email = (new TemplatedEmail())
                ->from('devel.magnum@gmail.com')
                ->to($curr_user->getEmail())
                ->subject('You\'ve recieved a new coupon')
                ->htmlTemplate('email/coupon.html.twig')
                ->context([
                  'coupon' => $coupon
                ]);
                $mailer->send($email);

            }
            $email = (new TemplatedEmail())
            ->from('devel.magnum@gmail.com')
            ->to($curr_user->getEmail())
            ->subject('About your order !')
            ->htmlTemplate('email/order.html.twig')
            ->context([
              'order' => $orders
            ]);
    
            $mailer->send($email);
            $em ->flush();
        return $this->render('payment/success.html.twig', ["orders" => $orders]);
    }

   
     /**
     * @Route("/cancel-url", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }

    /* Event Payment */


    /**
     * @Route("/checkoutEvent/{event}",name="checkout_event")
     * @param Event $event
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function checkoutEvent(Event $event)
    {
        Stripe::setApiKey('sk_test_51KUO54LURk8bHQH66NOA9MpsReXKIXeXkjRe76TuRYFEhjeWw4aFTG1OLaM0oYe62iZFrcGq4Q1kYDQ9ZjNVQeue00pExVMjwm');
        $total = $event->getPrix();
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'Event : '.$event->getName().' Payment',
                            'images' => ['https://www.thesu.org.uk/pageassets/events/events.jpg'],
                        ],
                        'unit_amount'  => $total*100,
                    ],
                    'description' => $event->getDescription(),
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url_event', ["session_id" => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url_event', [], UrlGeneratorInterface::ABSOLUTE_URL),

        ]);

        return $this->redirect($session->url, 303);
    }

    /**
     * @Route("/success-url-event", name="success_url_event")
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param UsersRepository $userRepository
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface|TransportExceptionInterface
     */
    public function successUrlEvent(Request $request,EventRepository $eventRepository,UsersRepository $userRepository,MailerInterface $mailer)
    {
        $id = $request->query->get('session_id');

        $user = $this->getUser();


        $event = $eventRepository->find($id);

        $event->addParticipant($user);
        $eventRepository->add($event);


        /* QR CODE */
        $webPath = $this->getParameter('kernel.project_dir').'/public';
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


        return $this->render('payment/successEvent.html.twig', ["event" => $event]);
    }

    /**
     * @Route("/cancel-url-event", name="cancel_url_event")
     */
    public function cancelUrlEvent(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }

}
