<?php

namespace App\Controller;

use App\Entity\Podcasts;
use App\Form\PodcastType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{
    /**
     * @Route("/podcast", name="app_podcast")
     */
    public function index(): Response
    {
        $podcastss= $this->getDoctrine()->getManager()->getRepository(Podcasts::class)->findAll();
        return $this->render('podcast/index.html.twig', [
            'p'=>$podcastss
        ]);
    }

    /**
     * @Route("/addpodcast", name="addpodcast")
     */
    public function addPodcast(Request $request): Response
    {
        $podcast = new Podcasts();
        $form = $this->createForm(PodcastType::class,$podcast);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $request->files->get('podcast')['image'];
           // $file=$jeux->getImagejeux();
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename=md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $podcast->setImage($filename);
            /*echo "<pre>";
            var_dump($file);
            die;*/
            $em= $this->getDoctrine()->getManager();
            $em->persist($podcast);
            $em->flush();
            return $this->redirectToRoute('app_podcast');
        }
        return $this->render('podcast/createpodcast.html.twig',[
            'f' =>$form->createView()]);

    }
    
        /**
     * @Route("/removePod/{id}", name="supp_pod")
     */
    public function suppressionPod(Podcasts  $podcast): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($podcast);
        $em->flush();

        return $this->redirectToRoute('app_podcast');
    }   
    
        /**
     * @Route("/modifPod/{id}", name="modifPod")
     */
    public function modifPod(Request $request,$id): Response
    {
        $podcast = $this->getDoctrine()->getManager()->getRepository(Podcasts::class)->find($id);

        $form = $this->createForm(PodcastType::class,$podcast);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('podcast')['image'];
            // $file=$jeux->getImagejeux();
             $uploads_directory = $this->getParameter('uploads_directory');
             $filename=md5(uniqid()) . '.' . $file->guessExtension();
             $file->move(
                 $uploads_directory,
                 $filename
             );
             $podcast->setImage($filename);
             /*echo "<pre>";
             var_dump($file);
             die;*/
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_podcast');
        }
        return $this->render('podcast/updatepod.html.twig',['f'=>$form->createView()]);
    }    
}
