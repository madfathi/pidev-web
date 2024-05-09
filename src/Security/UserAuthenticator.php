<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\UserRepository;


class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    private $session;



    public const LOGIN_ROUTE = 'app_login';
    private $userRepository;
    private $urlGenerator;

   public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session,UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->userRepository = $userRepository;
    }



    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->request->get('username', '');
    
        $request->getSession()->set(Security::LAST_USERNAME, $username);
    
        $user = $this->userRepository->findOneBy(['username' => $username]);

        $request->getSession()->set('user_id', $user->getId());
    
        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Check if the user has ROLE_ADMIN
        if ($token->getUser() && in_array('Admin', $token->getUser()->getRoles())) {
            // Redirect admin users to the admin dashboard
            return new  RedirectResponse ($this->urlGenerator->generate('app_user_homee'));
        }
        
        // Check if the user has ROLE_USER
        if ($token->getUser() && in_array('ROLE_USER', $token->getUser()->getRoles())) {
            // Redirect user users to the user dashboard
            return new  RedirectResponse ($this->urlGenerator->generate('app_main'));
        }

        // If no specific role matches, you can redirect to a default route
        return new  RedirectResponse ($this->urlGenerator->generate('app_main'));
    }
    
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}