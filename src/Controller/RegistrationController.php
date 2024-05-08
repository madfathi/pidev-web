<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Controller\UserController;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\SendMailService;
use App\Service\JWTService;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\AuthenticatedBadge;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;



class RegistrationController extends AbstractController
{
    
   

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager ,ParameterBagInterface $parameterBag, UserRepository $userRepository, VerifyEmailHelperInterface $verifyEmailHelper,MailerInterface $mailer ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            if($urlImage = $form['urlImage']->getData()){
                $photoDir = $parameterBag->get('photo_dir');
                $fileName = uniqid().'.'.$urlImage->guessExtension();
                $urlImage->move($photoDir, $fileName);
                $user->setUrlImage($fileName);
            }

            $user->setUrlImage($fileName);
            $entityManager->persist($user);
            $entityManager->flush();
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getAddEmail(),
                ['id' => $user->getId()]
            );

            $email = (new TemplatedEmail())
            ->from(new Address('amrou.bouslimi@esprit.tn', 'NoReply'))
            ->to($user->getAddEmail())
                ->subject('Verify Your Email Address')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'user' => $user,
                    'signedUrl' => $signatureComponents->getSignedUrl(),
                    'expiresAtMessageKey' => 'expiresAtMessageKey', // Adjust this message key as needed

                ]);

            $mailer->send($email);
            $this->addFlash('success', 'Your account has been created. Please check your email to verify your account.');


         

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        try {
            // Validate the email confirmation
            $verifyEmailHelper->validateEmailConfirmationFromRequest($request, $user->getId(), $user->getAddEmail());

            // Mark the user's email as verified
            $user->setIsVerified(true);
            $entityManager->flush();

            $this->addFlash('success', 'Your email has been verified. You can now log in.');
            return $this->redirectToRoute('app_login');
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }
    }

    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerController $mailer, MailerInterface $test): Response
    {
        $result = $mailer->sendEmail($test);
        return new Response($result);
    }
}



