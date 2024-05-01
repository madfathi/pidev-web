<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;

use App\Security\AppCustomAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registrate', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Encode the plain password
       
        

        $entityManager->persist($user);
        $entityManager->flush();

        // Add a success flash message
        $this->addFlash('success', 'Votre compte a été créé avec succès.');

        // Redirect to the login page or wherever appropriate
        return $this->redirectToRoute('app_login');
    }

    // Add an error flash message
    $this->addFlash('error', 'Une erreur est survenue lors de la création du compte.');

    return $this->render('registration/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}
    
}