<?php

namespace App\Controller;


use App\Entity\ Article;
use App\Entity\Commentaire;
use App\Entity\Podcasts;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Repository\PodcasterRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
        public function SupprimerPodcastsMobile(Request $request,ArticleRepository $articleRepository) {
            $id = $request->get("id");

            $article = $articleRepository->find($id);

           $articleRepository->remove($article);
    
                return new JsonResponse("Podcasts Supprime!", 200);

        }

    /**
     * @Route("/AjouterArticlesMobile", name="AjouterArticlesMobile")
     * @param Request $request
     * @param PodcasterRepository $podcasterRepository
     * @return JsonResponse|Response
     */
        public function AjouterArticleMobile(Request $request,UsersRepository $usersRepository)
        {
            $article = new Article();
            $article->setTitle($request->get("title"));
            $article->setUrl($request->get("url"));
            $article->setContent($request->get("content"));
            $article->setAuthorid($usersRepository->find(1));

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
    
                return new JsonResponse("Article Ajoute!", 200);
            }
            catch (\Exception $ex)
            {
                return new Response("Execption: ".$ex->getMessage());
            }
    
            //http://127.0.0.1:8000/AjouterArticlesMobile?Title=json&Description=json&Rating=5&Views=0&File=json&Image=json&idCategorie=8
    
    
        }


    /**
     * @Route("/AfficherCommentairesMobile",name="AfficherCommentairesMobile")
     */
        public function AfficherCommentairesMobile(Request $request,CommentaireRepository $commentaireRepository)
        {
            $comments = $commentaireRepository->findBy(["articleid"=>$request->get("id")]);

            //$comments = $commentaireRepository->findBy(["articleid"=>$request->get("id")]);
            return $this->json($comments,200,[]);

            //http://127.0.0.1:8000/AfficherCommentairesMobile?id=

        }


    /**
     * @Route("/SupprimerCommentaireMobile", name="SupprimerCommentaireMobile")
     */
    public function SupprimerCommentaireMobile(Request $request,CommentaireRepository $commentaireRepository) {

        $commentaire = $commentaireRepository->find($request->get("id"));
        if($commentaire!=null ) {
            $commentaireRepository->remove($commentaire);

            return new JsonResponse("Commentaire Supprime!", 200);
        }
        return new JsonResponse("ID Commentaire Invalide.");
        //http://127.0.0.1:8000/SupprimerCommentaireMobile?id=


    }

    /**
     * @Route("/AjouterCommentaireMobile", name="AjouterCommentaireMobile")
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param UsersRepository $usersRepository
     * @param CommentaireRepository $commentaireRepository
     * @return JsonResponse|Response
     */
    public function AjouterCommentaireMobile(Request $request,ArticleRepository $articleRepository,UsersRepository $usersRepository,CommentaireRepository $commentaireRepository)
    {
        $user = $usersRepository->find(1);
        $commentaire = new Commentaire();
        $commentaire->setArticleid($articleRepository->find($request->get('id')));
        $commentaire->setMessage($request->get('message'));
        $commentaire->setSubmitdate(new \DateTime());
        $commentaire->setUserid($user);

        try {
            $commentaireRepository->add($commentaire);
            return new JsonResponse("Commentaire Ajoute!", 200);
        } catch (OptimisticLockException $e) {
        } catch (\Exception $e) {
            printf($e->getMessage());
            return new JsonResponse("Erreur Ajout Commentaire!");

        }

        //http://127.0.0.1:8000/AjouterCommentaireMobile?id=json&message=json&date=json


    }
        
    
    
        
    }

