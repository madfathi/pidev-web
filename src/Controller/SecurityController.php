<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SendMailService;
use App\Form\ResetPasswordFormType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;







class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        
        
        if ($this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_user_index'));
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

       return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);

    }
    

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route(path: '/forget_pass', name: 'forget_pass')]
    public function forgettenPaswword(Request $request , UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator , EntityManagerInterface $entityManager , SendMailService $mail) : Response
    {
        $form =$this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $userRepository->findOneByAddEmail($form->get('addEmail')->getData());
            if($user)
            {
                // generate token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                //generate link for regenerate password 
                $url=$this->generateUrl('reset_pass', ['token' => $token],UrlGeneratorInterface::ABSOLUTE_URL);  
                
                // genere mail data 
                $context = compact('url', 'user');

                //send mail 

                $mail->send(
                    'no-reply@carthago.com',
                    $user->getAddEmail(),
                    'paswword renitialisation',
                    'password_reset',
                    $context
                );
                $this->addFlash('success', 'Mail sent succesufely');
                return $this->redirectToRoute('app_login');  
            }
       
        $this->addFlash('danger','un probelem est survenu');
         return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/reset_pass.html.twig', [
            'requestPassForm' => $form->createView() 
        ]);

        
    }

    #[Route('/oublier-pass{token}'  ,name:'reset_pass')]
        public function resetpass(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher) :Response
        {
            // if token exist in database 
            $user = $userRepository->findOneByResetToken($token);
            
            if($user)
            {
                $form = $this->createForm(ResetPasswordFormType::class);
                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    // delete token 
                    $user->setResetToken('');
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user ,
                            $form->get('password')->getData()

                        )
                    );
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('succes','password modified ');
                    return $this->redirectToRoute('app_login');
                }


                return $this->render('security/reset.html.twig', ['passForm' => $form->createView()]);
            }
            $this->addFlash('danger','invalid token ');
            return $this->redirectToRoute('app_login');
        }

}
