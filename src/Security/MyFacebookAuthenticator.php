<?php
namespace App\Security;

use App\Entity\Master\MstStatus;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\SystemApp\AppUserInfo;
use App\Entity\User; // your user entity
use App\Repository\SystemApp\AppUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MyFacebookAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    private $appUserRepository;

    public function __construct(AppUserRepository $appUserRepository,ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->appUserRepository = $appUserRepository;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        // For Symfony lower than 3.4 the supports method need to be called manually here:
        // if (!$this->supports($request)) {
        //     return null;
        // }

        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()
            ->fetchUserFromToken($credentials);
        $email = $facebookUser->getEmail();
        $firstName = $facebookUser->getFirstName();
        $lastName = $facebookUser->getLastName();

        dd($credentials);
        // 1) have they logged in with Facebook before? Easy!
        $existingUser = $this->appUserRepository->findOneBy(['facebookId' => $facebookUser->getId()]);
        if ($existingUser) {
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
        $user->setFacebookId($facebookUser->getId());
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @return FacebookClient
     */
    private function getFacebookClient()
    {
        return $this->clientRegistry
            // "facebook_main" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('facebook_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('app_homepage');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // ...
}