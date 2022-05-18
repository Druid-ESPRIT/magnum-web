<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Subscription;
use Symfony\Component\Security\Core\Security;

class SubscriptionController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    /**
     * @Route("/subscription", name="app_subscription")
     */
    public function index(): Response
    {
        return $this->render('subscription/index.html.twig', [
            'controller_name' => 'SubscriptionController',
        ]);
    }
    /**
     * @Route("/back/subscriptionchecker", name="subscriptionchecker")
     */
    public function subscriptionChecker(): Response
    {
        $repository=$this->getDoctrine()->getRepository(Subscription::class);
        $subs=$repository->findAll();
        return $this->render('subscription/subscriptionback.html.twig', ["subs"=>$subs]
        );
    }

       /**
     * @Route("/mysubscriptions", name="mysubscriptions")
     */
    public function mySubscriptionsList(): Response
    {
        $curr_user = $this->security->getUser();  
        $repository=$this->getDoctrine()->getRepository(Subscription::class);
        $subs=$repository->findBy(['userId' => $curr_user->getID()]);
        return $this->render('subscription/mysubscriptions.html.twig', ["subs"=>$subs]);
    }
}
