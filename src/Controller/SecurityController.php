<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SendMailService;
use App\Form\ResetPasswordFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Service\SmsGenerator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;





class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_user_home'));
        }
    
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        $user2 = $this->getDoctrine()->getRepository(User::class)->findOneBy(['password' => $password]);

        if ($user && $user2) {
            $iduser = $user->getIduser();
               $session->set('iduser', $iduser);
        // Stocker les informations de l'utilisateur dans la session
      
        }
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    
}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/forgot', name: 'forgot')]
    public function forgotPassword(Request $request , UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator , EntityManagerInterface $entityManager ,MailerInterface $mailer,MailerController $mailer1,UrlGeneratorInterface $urlGenerator)
    {
        $form =$this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $donnees = $form->getData();
            $user = $userRepository->findOneBy(['addEmail'=>$donnees]);
            if(!$user)
            {
                $this->addFlash('danger','un probelem est survenu');
                return $this->redirectToRoute('forgot');  

            }
            $token = $tokenGenerator->generateToken();
               try{
                $user->setResetToken($token);
               // $entityManager->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

               }catch(\Exception $exception){

                $this->addFlash('danger','un probelem est survenu :' .$exception->getMessage());
                return $this->redirectToRoute('app_login');
               }

               $resetUrl = $urlGenerator->generate('app_reset_password', array('token'=>$token), UrlGeneratorInterface::ABSOLUTE_URL);
               $email = (new TemplatedEmail())
               ->from('fathimaddeh88@gmail.com')
               ->to($user->getAddEmail())
               ->subject('Réinitialisation de mot de passe')
               ->html(
                   $this->renderView(
                       'emails/password_reset.html.twig',
                       ['resetUrl' => $resetUrl, 'user' => $user, 'url' => $resetUrl]
                   )
               );
               $mailer->send($email); 
    
               $this->addFlash('success', 'Mail sent succesufely');
            }
            return $this->render('security/reset_pass.html.twig', [
                'form' => $form->createView() 
            ]);
    
            }

        
        
     #[Route('/reset-password/reset/{token}', name: 'app_reset_password', methods: ['GET','POST'])]

    public function resetpassword(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->findOneBy(['reset_token' => $token]);

        if ($user === null) {
            $this->addFlash('danger', 'TOKEN INCONNU');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $user->setResetToken(null);
            $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('password')));
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Mot de passe mis à jour :');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/reset.html.twig', ['token' => $token]);
        }
    }

}
