<?php

namespace App\Security;

use App\Repository\SystemApp\AppUserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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


class PortalAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $appUserRepository;
    private $router;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $session;

    public function __construct(AppUserRepository $appUserRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session)
    {
        $this->appUserRepository = $appUserRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
    }

    public function supports(Request $request): bool
    {
        // do your work when we're POSTing to the login page
        return $request->attributes->get('_route') === 'login-email'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'userName' => $request->request->get('userName'),
            'userPassword' => $request->request->get('userPassword'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['userName']);

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

        if ($user->getAppUserCategory()->getId() == 1) {
            // If the user is a backend user fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Please login via backend application.');
        }

        return $user;

    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $password = $this->passwordEncoder->isPasswordValid($user, $credentials['userPassword']);

        if (!$password) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('comment.userPassword.invalid');

        }
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        $redirectURLName = $this->session->get('redirectURLName', '');
        $redirectSubEventId = $this->session->get('redirectSubEventId', '');

        if($this->session->has('returnToRequest') && $this->session->get('returnToRequest') != '') {
            return new RedirectResponse($this->session->get('returnToRequest'));
        }

        if (!empty($redirectURLName) && !empty($redirectSubEventId)){
            return new RedirectResponse($this->router->generate($redirectURLName, ['id' => $redirectSubEventId]));
        }

        return new RedirectResponse($this->router->generate('personal-info'));


    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('login-email');
    }
}
