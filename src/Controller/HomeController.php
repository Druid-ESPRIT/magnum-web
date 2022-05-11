<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function index(): Response
    {
        return $this->render("frontend/home/index.html.twig", []);
    }

    public function backend_index(): Response
    {
        return $this->render("backend/index.html.twig", []);
    }
}
