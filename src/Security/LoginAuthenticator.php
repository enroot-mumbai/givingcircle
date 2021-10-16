<?php

namespace App\Security;

use App\Entity\SystemApp\AppUser;
use App\Repository\SystemApp\AppUserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $appUserRepository;
    private $router;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(AppUserRepository $appUserRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->appUserRepository = $appUserRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {
        // do your work when we're POSTing to the login page
        return $request->attributes->get('_route') === 'login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'userName' => $request->request->get('userName'),
            'userPassword' => $request->request->get('userPassword'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        //		 $request->getSession()->set(Security::LAST_USERNAME, $credentials['userName']);

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->appUserRepository->findOneBy(['userName' => $credentials['userName']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('comment.userName.not_found');
        }

        if ($user->getIsActive() == 0) {
            // If the user access is disabled
            throw new CustomUserMessageAuthenticationException('Your access to the Portal is disabled.');
        }

        if ($user->getAppUserCategory()->getId() != 1) {
            // If the user is a backend user fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Please login via application.');
        }

        return $user;

    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $this->passwordEncoder->isPasswordValid($user, $credentials['userPassword']);

        if (!$password) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('comment.userPassword.invalid');

        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

        return new RedirectResponse($this->router->generate('core_welcome'));
        //return $this->router->generate('welcome');

    }

    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }
}
