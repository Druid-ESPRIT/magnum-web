<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="app_categorie")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/index.html.twig', [
            'c' => $categories
        ]);
    }
    
    /**
     * @Route("/adminc", name="adminc")
     */
    public function indexAdmin(): Response
    {
        return $this->render('admin/index.html.twig');
    }


    /**
     * @Route("/addcategorie", name="addcategorie")
     */
    public function addCategorie(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/createcateg.html.twig', [
            'f' => $form->createView()
        ]);
    }

    /**
     * @Route("/removeCateg/{id}", name="supp_categ")
     */
    public function suppressionCateg(Categorie  $categorie): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();

        return $this->redirectToRoute('app_categorie');
    }

    /**
     * @Route("/modifCateg/{id}", name="modifCateg")
     */
    public function modifCateg(Request $request, $id): Response
    {
        $categorie = $this->getDoctrine()->getManager()->getRepository(Categorie::class)->find($id);

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_categorie');
        }
        return $this->render('categorie/updatecateg.html.twig', ['f' => $form->createView()]);
    }
}
