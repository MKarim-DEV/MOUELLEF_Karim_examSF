<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
        public function add(Request $request, SluggerInterface $slugger,UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em, ParameterBagInterface $parameterBag): Response
            {   $user = new User();
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);
        
                if($form->isSubmitted() && $form->isValid()){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $picture = $form->get('picture')->getData();
                    if(is_null($picture)){
                        $error = new FormError("Veuillez uploader une image");
                        $form->get('picture')->addError($error);
                    } else {
                        $uploadDir = $parameterBag->get('user_picture_directory');
                        $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();
        
                        try {
                            $picture->move(
                                $uploadDir, // Utilisez le chemin du dossier de stockage obtenu depuis le paramètre de configuration
                                $newFilename
                            );
                        } catch (FileException $e) {
                        dd($e);
                        }
        
                        $form = $form->getData();
                        $form->setPicture($newFilename);
                        // $form->setUserAdd($this->getUser());
                        // $em->persist($form);
                        // $em->flush();
        
                        // return $this->redirectToRoute("app_user_index");
                    }
                    $em->persist($user);
                    $em->flush();
        
                    return $this->redirectToRoute("app_user_index", [], Response::HTTP_SEE_OTHER);
                }
        
                return $this->render('user/new.html.twig', [
                    "user"=>$user,
                    "form"=> $form->createView()
                ]);
            }
        
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RH')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RH')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
