<?php

namespace App\Controller;


use App\Entity\ Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;


class ArticleApiController extends AbstractController
{
   
    
        /**
         * @Route("/AfficherArticlesMobile", name="AfficherArticlessMobile")
         */
        public function AfficherPodcastsMobile(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $commandes = $em->getRepository(Article::class)->findAll();
    
            return $this->json($commandes,200,[],['groups'=>'post:read']);
    
            //http://127.0.0.1:8000/AfficherArticlesMobile
    
        }
        /**
         * @Route("/SupprimerArticlesMobile", name="SupprimerArticlesMobile")
         */
        public function SupprimerPodcastsMobile(Request $request) {
            $id = $request->get("id");
            $em = $this->getDoctrine()->getManager();
            $commande = $em->getRepository(Podcasts::class)->find($id);
            if($commande!=null ) {
                $em->remove($commande);
                $em->flush();
    
                return new JsonResponse("Podcasts Supprime!", 200);
            }
            return new JsonResponse("ID Podcasts Invalide.");
    
    
        }
        /**
         * @Route("/AjouterArticlesMobile", name="AjouterArticlesMobile")
         */
        public function AjouterArticleMobile(Request $request)
        {
            $commande = new Article();
            $commande->setTitle($request->get("title"));
            $commande->setUrl($request->get("url"));
            $commande->setContent($request->get("Content"));
            
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($commande);
                $em->flush();
    
                return new JsonResponse("Article Ajoute!", 200);
            }
            catch (\Exception $ex)
            {
                return new Response("Execption: ".$ex->getMessage());
            }
    
            //http://127.0.0.1:8000/AjouterPodcastsMobile?Title=json&Description=json&Rating=5&Views=0&File=json&Image=json&idCategorie=8
    
    
        }
        
    
    
        
    }

