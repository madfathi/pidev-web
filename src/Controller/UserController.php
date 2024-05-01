<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Fidelite;
use App\Form\FideliteType;
use App\Entity\User;
use App\Form\AccountFormType;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/readd', name: 'app_user_i', methods: ['GET'])]
    public function read(): Response
    {
    
        $repository= $this->getDoctrine()->getRepository(User::class)->findAll();
     
        return $this->render('User/read.html.twig',['users'=>$repository,
    ]);
}
#[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('User with id ' . $id . ' not found');
    }

    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Le formulaire a été soumis et est valide, donc nous pouvons sauvegarder les modifications
        $entityManager->flush();
        
        $this->addFlash('success', "L'utilisateur a bien été modifié.");

        return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->render('user/edit.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}



#[Route('/user/add', name: 'app_user_add')]
public function afficherFormulaire(Request $request,EntityManagerInterface $entityManager): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       

     
    
        $entityManager->persist($user);
        $entityManager->flush();
            $this->addFlash('message','l utulisateur a bien ete ajouter ');
            return $this->redirectToRoute('app_user_i', [], Response::HTTP_SEE_OTHER);
        }
    

    return $this->render('user/form.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}

#[Route('/backrechercheAjax', name: 'backrechercheAjax')]
public function searchAjax(Request $request, UserRepository $userRepository): Response
{
    $query = $request->query->get('q');
    $donnees = $userRepository->findByNom($query); // Assurez-vous d'ajuster cette méthode selon votre logique de recherche réelle dans le UserRepository

    return $this->render('base.html.twig', [
        'donne' => $donnees,
    ]);
}
#[Route('/{id}/delets', name: 'app_user_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User with id ' . $id . ' not found');
        }
    
        $entityManager->remove($user);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_user_i', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/singin', name: 'app_singin')]
    public function login(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $mdp = $request->request->get('mdp');
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => $email]);
    
            if (!$user || $mdp != $user->getMdp()) {
                return $this->redirectToRoute('app_singin', ['error' => 'Invalid credentials']);
            }
    
            $session->set('user_id', $user->getId());
    
            if ($user->isIsAdmin()) {
                return $this->redirectToRoute('app_user');
            } else {
                return $this->redirectToRoute('app_login');
            }
        }
    
        return $this->render('frontOffice/contact.html.twig');
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): Response
    {
        // Remove the user_id session variable
        $session->remove('user_id');
        
        // Redirect the user to the login page after logout
        return $this->redirectToRoute('app_login');
    }
    #[Route('/tri-par-nom', name: 'app_tri_nom')]
    public function triParNom(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['nom' => 'ASC']); // Tri par nom ascendant

        return $this->render('user/read.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/bannir-utilisateur/{id}', name: 'app_user_ban')]
    
    public function bannirUtilisateur($id): Response
    {
        // Récupérer l'utilisateur à bannir (remplacez User par le nom de votre entité utilisateur)
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(User::class)->find($id);

        if (!$utilisateur) {
            throw $this->createNotFoundException('Utilisateur non trouvé avec l\'identifiant: '.$id);
        }

        // Implémentez ici la logique pour bannir l'utilisateur

        // Par exemple, vous pouvez modifier une propriété isBanned de l'utilisateur
        $utilisateur->setIsBanned(true);

        // Enregistrez les modifications dans la base de données
        $entityManager->flush();

        // Redirigez ou renvoyez une réponse appropriée
        return $this->redirectToRoute('user/read.html.twig');
    }

    #[Route('/newadmin/add', name: 'app_newadmin_add')]
    public function addUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(AccountFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

