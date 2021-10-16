<?php

namespace App\Controller\Security;

use App\Entity\SystemApp\AppUser;
use App\Form\Security\UserForgotPasswordType;
use App\Form\Security\UserResetPasswordType;
use App\Repository\SystemApp\AppUserRepository;
use App\Service\Mailer;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     * @Route("/", name="index")
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function loginForm(AuthenticationUtils $authenticationUtils)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('core_home');
        }

			// get the login error if there is one
      $error = $authenticationUtils->getLastAuthenticationError();
      // last username entered by the user
      $lastUsername = $authenticationUtils->getLastUsername();
      
			return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/forgotpassword", name="forgot_password")
     * @param AppUserRepository $appUsersRepository
     * @param Request $request
     * @param Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function forgotPassword(AppUserRepository $appUsersRepository, Request $request, Mailer $mailer)
    {
        $form = $this->createForm(UserForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $appUser = $appUsersRepository->findOneBy(['userName' => $form->get('userName')->getData()]);
            if (!$appUser instanceof AppUser) {

                $this->addFlash('danger', 'Username is not available in the system');

            } else {
                $userEmail = $appUser->getAppUserInfo()->getUserEmail();
                $userFirstName = $appUser->appUserInfo->getUserFirstName();
                $userLastName = $appUser->appUserInfo->getUserLastName();
                $userFullName = $userFirstName .' '. $userLastName;
                $tokenExpiryTime = new DateTime('+24 hour');
                $token = bin2hex(random_bytes(32));
                $appUser->setUserResetPasswordToken($token);
                $appUser->setUserResetPasswordTokenExpiry($tokenExpiryTime);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($appUser);
                $entityManager->flush();
                // Send Email to user
                $mailer->mailerForgotPassword($userFullName, $userEmail, $token);
                $this->addFlash('success', 'An email has been send to help you reset your password.');
                return $this->redirectToRoute('forgot_password');
            }
        }
        return $this->render('security/forgotpassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/resetpassword/{token}", name="reset_password")
     * @param Request $request
     * @param AppUserRepository $appUsersRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param string $token
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @return Response
     */
    public function resetPassword(Request $request, AppUserRepository $appUsersRepository, UserPasswordEncoderInterface $encoder, string $token, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $errorMessages = array();
        if (strlen($token) != 64) {
            $errorMessages[] = 'The validation key is invalid. Please make sure that you have clicked on the reset password link send on your email address.';
        }
        $currentDateTime = date('Y-m-d H:i:s');
        $checkTokenExpiry = $appUsersRepository->checkValidationKey($token, $currentDateTime);
        if ($checkTokenExpiry == null) {
            $errorMessages[] = 'Your validation key has expired. Please click on forgot password to reset the password';
        }
        $appUser = $appUsersRepository->findOneBy(['userResetPasswordToken' => $token]);
        if (!$appUser instanceof AppUser) {
            $errorMessages[] = 'Invalid User';
        }
        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('newPassword')->getData();
            $password = $encoder->encodePassword($appUser, $plainPassword);
            $appUser->setUserPassword($password);
            $appUser->setUserResetPasswordToken(null);
            $appUser->setUserResetPasswordTokenExpiry(null);
            $this->getDoctrine()->getManager()->flush();
            $token = new UsernamePasswordToken($appUser, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            $this->addFlash('success', "Your have successfully reset your password and you will be now redirected to login page.");
            return $this->redirectToRoute('logout');
        }
        return $this->render('security/resetpassword.html.twig', [
            'messages' => $errorMessages,
            'label_message' => 'info.create_password',
            'label_button' => 'action.reset_password',
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/createpassword/{token}", name="create_password")
     * @param Request $request
     * @param AppUserRepository $appUsersRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param string $token
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @return Response
     */
    public function createPassword(Request $request, AppUserRepository $appUsersRepository, UserPasswordEncoderInterface $encoder, string $token, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $errorMessages = array();
        if (strlen($token) != 64) {
            $errorMessages[] = 'The validation key is invalid. Please make sure that you have clicked on the create password link send on your email address.';
        }
        $appUser = $appUsersRepository->findOneBy(['userCreationToken' => $token]);
        if (!$appUser instanceof AppUser) {
            $errorMessages[] = 'Invalid User';
        }
        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('newPassword')->getData();
            $password = $encoder->encodePassword($appUser, $plainPassword);
            $appUser->setUserPassword($password);
            $appUser->setUserCreationToken(null);
            $this->getDoctrine()->getManager()->flush();
            $token = new UsernamePasswordToken($appUser, $password, 'main');
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));
            $this->addFlash('success', "Your have successfully created your password and you will be now redirected to login page.");
            return $this->redirectToRoute('logout');
        }
        return $this->render('security/resetpassword.html.twig', [
            'messages' => $errorMessages,
            'label_message' => 'info.create_password',
            'label_button' => 'action.create_password',
            'form' => $form->createView(),
        ]);
    }
}
