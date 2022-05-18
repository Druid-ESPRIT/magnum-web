<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Podcasters;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    public function encryptWithDefaultAlgorithm(string $password): string {
        return password_hash(
            $password,
            PASSWORD_DEFAULT
        );
    }
    public function extractAvatar(string $originalFilename): string {
        $originalFilename = pathinfo(
            $avatarFile->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        
        $safeFilename = transliterator_transliterate(
            "Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()",
            $originalFilename
        );
        
        $newFilename =
                     $safeFilename .
                     "-" .
                     uniqid() .
                     "." .
                     $avatarFile->guessExtension();

        try {
            // Move the file to the directory where avatars are stored
            $avatarFile->move(
                $this->getParameter("avatar_directory"),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }
    
    public function registerUser($form): Response {
        $user = new Users();
        $man  = $this->getDoctrine()->getManager();
        $repo = $man->getRepository(Users::class);

        $username = $form->get('username')->getData();
        $email    = $form->get('email')->getData();
        $password = $form->get('password')->getData();
        $avatar   = $form->get('avatar')->getData();
        
        $_match = $repo->findBy([
            'username' => $username,
            'email' => $email
        ]);
        
        if ($_match) {
            $errors[] = [
                "message" =>
                "Someone with the same username/email is already registered."
            ];
            
            return $this->redirectToRoute("home", [
                "form" => $form->createView(),
                "errors" => $errors,
            ]);
        }
        
        $secPassword = $this->encryptWithDefaultAlgorithm($password);

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($secPassword);
        $user->setStatus("Active");

        if ($avatar) {
            $newFilename = $this->extractAvatar($avatar);
            $user->setAvatar($newFilename);
        }
        
        $man->persist($user);
        $man->flush();
        
        return $this->redirectToRoute("app_login");
    }

    public function registerPodcaster($form): Response {
        $podcaster = new Podcasters();
        $man = $this->getDoctrine()->getManager();
        $repo = $man->getRepository(Podcasters::class, $podcaster);

        $_usr_match = $repo->findOneBy(['username' => $form->get('username')->getData()]);
  
        if ($_usr_match) {
            $_pod_match = $repo->findBy(['username' => $_usr_match->getUsername()]);
            if ($_pod_match) {
                $errors[] = [
                    "message" =>
                    "A user/podcaster with the same username/email is already registered.",
                ];
                return $this->redirectToRoute("home", [
                    "form" => $form->createView(),
                    "errors" => $errors,
                ]);            
            }
        }
        
        $rawPassword = $form->get('password')->getData();
        $secPassword = $this->encryptWithDefaultAlgorithm($rawPassword);

        $podcaster->setUsername($form->get('username')->getData());
        $podcaster->setEmail($form->get('email')->getData());
        $podcaster->setStatus('Active');
        $podcaster->setPassword($secPassword);
        $podcaster->setFirstName($form->get('firstName')->getData());
        $podcaster->setLastName($form->get('lastName')->getData());
        $podcaster->setBiography($form->get('biography')->getData());

        $avatarFile = $form->get('avatar')->getData();
        if ($avatarFile) {
            $newFilename = $this->extractAvatar($avatarFile);
            $user->setAvatar($newFilename);
        }
        
        $man->persist($podcaster);
        $man->flush();
        
        return $this->redirectToRoute("app_login");
    }

    public function delegate($form): Response {
        $roleCheckbox = $form->get('role')->getData();
        
        if ($roleCheckbox === true) {
            return $this->registerPodcaster($form);
        } else {
            return $this->registerUser($form);
        }
    }

    public function register(Request $request): Response
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->delegate($form);
        }

        return $this->render("register/index.html.twig", [
            "form" => $form->createView(),
            "errors" => [],
        ]);
        
    }
}
