<?php

namespace App\Security;

use App\Entity\Master\MstStatus;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\User;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserInfo;
use Doctrine\ORM\EntityManagerInterface;
#use FOS\UserBundle\Model\UserManagerInterface;
use App\Repository\SystemApp\AppUserRepository;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AppUserRepository
     */
    private $appUserRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * GoogleAuthenticator constructor.
     * @param ClientRegistry $clientRegistry
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     * @param AppUserRepository $appUserRepository
     * @param ParameterBagInterface $params
     * @param SessionInterface $session
     */
    public function __construct(ClientRegistry $clientRegistry, RouterInterface $router, EntityManagerInterface $em, AppUserRepository
    $appUserRepository, ParameterBagInterface $params, SessionInterface $session)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->appUserRepository = $appUserRepository;
        $this->router = $router;
        $this->params = $params;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getParameter($parameter)
    {
        return $this->params->get($parameter);
    }

    /**
     * @param Request $request
     * @return \League\OAuth2\Client\Token\AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null|object|\Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()
            ->fetchUserFromToken($credentials);

        $email = $googleUser->getEmail();
        $firstName = $googleUser->getFirstName();
        $lastName = $googleUser->getLastName();
        // 1) have they logged in with Google before? Easy!
        #$existingUser = $this->em->getRepository(User::class)
        #    ->findOneBy(['googleId' => $googleUser->getId()]);
        $existingUser = $this->appUserRepository->findOneBy(['googleId' => $googleUser->getId()]);
        if ($existingUser ) {
            $user = $existingUser;
        } else {

            // 2) do we have a matching user by email?
            $user = $this->appUserRepository->findOneBy(['userName' => $email]);
            if (empty($user)) {
                $user  = $this->appUserRepository->getUserByEmailId($email);
                if (!empty($user) && !empty($user[0]) )
                    $user = $user[0];
            }
            if (!empty($user) ) {
                if ($user->getIsActive() == 0) {
                    // If the user access is disabled
                    throw new CustomUserMessageAuthenticationException('Your access to the Portal is disabled.');
                }

                if ($user->getAppUserCategory()->getId() == 1) {
                    // If the user is a backend user fail authentication with a custom error
                    throw new CustomUserMessageAuthenticationException('Please login via backend application.');
                }
            } else {
                $objAppUserInfo = new AppUserInfo();
                $objAppUserInfo->setUserEmail($email);
                $objAppUserInfo->setUserFirstName($firstName);
                $objAppUserInfo->setUserLastName($lastName);
                $objAppUserInfo->setMstUserMemberType($this->em->getRepository(MstUserMemberType::class)->findOneBy(['userMemberType' => 'Individual']));
                $objAppUserInfo->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $objAppUserInfo->setOrgCompany($this->em->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $this->em->persist($objAppUserInfo);

                $user = new AppUser();
                $user->setUsername($email);
                $token = bin2hex(random_bytes(32));
                $user->setUserPassword($token);
                $user->setRowId(Uuid::uuid4()->toString());
                $appRole = array('ROLE_B2C_USER');
                $user->setRoles($appRole);
                $user->setUserCreationDateTime(new DateTime('now'));
                $user->setUserCreationToken($token);
                $objAppUserCategory = $this->em->getRepository(AppUserCategory::class)->findOneBy(['userCategory' => 'Application', 'isActive' => 1]);
                $user->setAppUserCategory($objAppUserCategory);
                $tokenExpiryTime = new DateTime('+24 hour');
                $user->setUserResetPasswordToken($token);
                $user->setUserResetPasswordTokenExpiry($tokenExpiryTime);
                $entityManager = $this->em;
                $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
                $user->setIsActive(1);
                $user->setMstStatus($objMstStatus);
                $user->setAppUserInfo($objAppUserInfo);
            }
        }

        // 3) Maybe you just want to "register" them by creating
        // a User object
        $user->setGoogleId($googleUser->getId());
        $this->em->persist($user);
        $this->em->flush();
        return $userProvider->loadUserByUsername($user->getUsername());
    }

    /**
     * @return GoogleClient
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google');
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $redirectURLName = $this->session->get('redirectURLName', '');
        $redirectSubEventId = $this->session->get('redirectSubEventId', '');
        if (!empty($redirectURLName) && !empty($redirectSubEventId)){
            return new RedirectResponse($this->router->generate($redirectURLName, ['id' => $redirectSubEventId]));
        }
        // on success, let the request continue
        return new RedirectResponse($this->router->generate('personal-info'));
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return null|Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}