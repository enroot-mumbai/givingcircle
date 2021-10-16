<?php

namespace App\Controller\Portal;

use App\Entity\Master\MstStatus;
use App\Entity\Master\MstUserMemberType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\Transaction\TrnAuthOtp;
use App\Form\SystemApp\AppUserPortalRegistrationType;
use App\Form\SystemApp\AppUserOrgPortalRegistrationType;
use App\Repository\Master\MstCountryRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Service\CommonHelper;
use App\Service\Mailer;
use App\Service\SendSMS;
use App\Service\FileUploaderHelper;
use Cassandra\Date;
use DateTime;
use DateTimeZone;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationLoginController extends AbstractController
{

    /**
     * @Route("/login-email", name="login-email", methods={"GET", "POST"})
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginEmail(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('portal/login/login-email.html.twig', [
            'controller_name' => 'RegistrationLoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/login-mobile", name="login-mobile", methods={"GET", "POST"})
     * @param Request $request
     * @param MstCountryRepository $mstCountryRepository
     * @return Response
     */
    public function loginMobile(Request $request, MstCountryRepository $mstCountryRepository): Response
    {
        $arrPhoneCodes = $mstCountryRepository->getCountryCodes();
        return $this->render('portal/login/login-mobile.html.twig', array( 'arrPhoneCodes' => $arrPhoneCodes));
    }

    /**
     * @Route("/sign-up", name="sign-up", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function signUp(Request $request): Response
    {
        return $this->render('portal/login/signUp.html.twig');
    }

    /**
     * @Route("/forgot-password", name="forgot-password", methods={"GET", "POST"})
     * @param AppUserRepository $appUsersRepository
     * @param Request $request
     * @param Mailer $mailer
     * @param SessionInterface $session
     * @return Response
     * @throws \Exception
     */
    public function forgotPassword(AppUserRepository $appUsersRepository, Request $request, Mailer $mailer, SessionInterface $session): Response
    {
        if($request->isMethod('POST')) {
            $strUserName = $request->get('emailId');
            $appUser = $appUsersRepository->getUserByEmailId($strUserName);
            if (!empty($appUser) && !empty($appUser[0])){
                $appUser = $appUser[0];
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

                $session->set('appUserId', $appUser->getId());

                return $this->redirectToRoute('forgot-password-submit');
            } else {
                $this->addFlash('danger', 'Username is not available in the system');
            }
        }
        return $this->render('portal/login/forgot-password.html.twig');
    }

    /**
     * @Route("/forgot-password-submit", name="forgot-password-submit", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function forgotPasswordSubmit(Request $request, SessionInterface $session): Response
    {
        $session->get('appUserId');
        $appUserId = $session->get('appUserId');
        $userEmail = "";
        if (!empty($appUserId)) {
            $entityManager = $this->getDoctrine()->getManager();
            $objAppUser = $entityManager->getRepository(AppUser::class)->findOneBy(["id" => $appUserId]);
            $objAppUserInfo = $objAppUser->getAppUserInfo();
            $userEmail = $objAppUserInfo->getUserEmail();
        }
        return $this->render('portal/login/forgot-password-submitted.html.twig', array('email_sent' => $userEmail));
    }

    /**
     * @Route("/login-mobile-generate-otp", name="login-mobile-generate-otp", methods={"POST"})
     * @param Request $request
     * @param CommonHelper $commonHelper
     * @param SendSMS $objSendSMS
     * @return Response
     */
    public function loginMobileGenerateOTP(Request $request, CommonHelper $commonHelper, SendSMS $objSendSMS): Response
    {
        //Generate OTP
        $strGenerateOTP = $commonHelper->generateNumericOTP(6);
        $countryCode = $request->get('countryCode');
        $mobNumber   = $request->get('mob_number');

        //save OTP to DB
        $entityManager = $this->getDoctrine()->getManager();
        $objTrnAuthOtp = new TrnAuthOtp();
        $objTrnAuthOtp->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
        $objTrnAuthOtp->setCreatedOn(new DateTime());
        $date = new DateTime();
        $date->modify("+2 minutes");
        $objTrnAuthOtp->setExpiredOn($date);
        $objTrnAuthOtp->setIsValid(1);
        $objTrnAuthOtp->setMobileNumber($mobNumber);
        $objTrnAuthOtp->setOtp($strGenerateOTP);
        $objTrnAuthOtp->setUserIpAddress($_SERVER['SERVER_ADDR']);
        $entityManager->persist($objTrnAuthOtp);
        $entityManager->flush();
        //Send OTP SMS
        $objSendSMS->sendSMS($mobNumber,"Your OTP for mobile verification is $strGenerateOTP. Team, Giving Circle.", 'loginMobileGenerateOTP');
die;
        return $this->render('portal/login/login-with-mobile-enter-otp.html.twig', array('mobile_number' => '98####3555'));
    }

    /**
     * @Route("/sign-up-individual", name="sign-up-individual", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function signUpIndividual(Request $request, SessionInterface $session, UserPasswordEncoderInterface $encoder): Response
    {
        $appUser = new AppUser();
        $form = $this->createForm(AppUserPortalRegistrationType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(32));
            $appUser->setRowId(Uuid::uuid4()->toString());
            $appRole = array('ROLE_B2C_USER');
            $appUser->setRoles($appRole);
            $appUser->setUserCreationDateTime(new DateTime('now'));
            $appUser->setUserCreationToken($token);
            $appUser->setUserPassword($encoder->encodePassword($appUser, $appUser->getUserPassword()));

            $appUser->getAppUserInfo()->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $objAppUserCategory = $this->getDoctrine()->getRepository(AppUserCategory::class)->findOneBy(['userCategory' => 'Application', 'isActive' => 1]);
            $appUser->setAppUserCategory($objAppUserCategory);
            $tokenExpiryTime = new DateTime('+24 hour');

            $appUser->setUserResetPasswordToken($token);
            $appUser->setUserResetPasswordTokenExpiry($tokenExpiryTime);
            $entityManager = $this->getDoctrine()->getManager();
            $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Pending Activation']);
            $appUser->setIsActive(1);
            $appUser->setMstStatus($objMstStatus);
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            $objMstUserMemberType = $entityManager->getRepository(MstUserMemberType::class)->findOneBy(["userMemberType" => 'Individual']);
            $objAppUserInfo =  $appUser->getAppUserInfo();
            $objAppUserInfo->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objAppUserInfo->setMstUserMemberType($objMstUserMemberType);
            $objAppUserInfo->setMobileCountryCode('+91');
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            //$this->addFlash('success', 'form.created_successfully');
            $session->set('appUserId', $appUser->getId());
            return $this->redirectToRoute('submit-sign-up');
        }
        return $this->render('portal/login/sign-up-individual.html.twig', [
            'user' => $appUser,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sign-up-organization", name="sign-up-organization", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function signUpOrganization(Request $request, SessionInterface $session, UserPasswordEncoderInterface $encoder): Response
    {
        $appUser = new AppUser();
        $form = $this->createForm(AppUserOrgPortalRegistrationType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(32));
            $appUser->setRowId(Uuid::uuid4()->toString());
            $appRole = array('ROLE_B2C_USER');
            $appUser->setRoles($appRole);
            $appUser->setUserCreationDateTime(new DateTime('now'));
            $appUser->setUserCreationToken($token);
            $appUser->setUserPassword($encoder->encodePassword($appUser, $appUser->getUserPassword()));
            $appUser->getAppUserInfo()->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $objAppUserCategory = $this->getDoctrine()->getRepository(AppUserCategory::class)->findOneBy(['userCategory' => 'Application', 'isActive' => 1]);
            $appUser->setAppUserCategory($objAppUserCategory);
            $tokenExpiryTime = new DateTime('+24 hour');
            $appUser->setUserResetPasswordToken($token);
            $appUser->setUserResetPasswordTokenExpiry($tokenExpiryTime);
            $entityManager = $this->getDoctrine()->getManager();
            $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Pending Activation']);
            $appUser->setIsActive(1);
            $appUser->setMstStatus($objMstStatus);
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            $objMstUserMemberType = $entityManager->getRepository(MstUserMemberType::class)->findOneBy(["userMemberType" => 'Organization']);
            $objAppUserInfo =  $appUser->getAppUserInfo();
            $objAppUserInfo->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objAppUserInfo->setMstUserMemberType($objMstUserMemberType);
            $entityManager->persist($appUser);
            $this->getDoctrine()->getManager()->flush();
            //$this->addFlash('success', 'form.created_successfully');
            $session->set('appUserId', $appUser->getId());
            return $this->redirectToRoute('submit-sign-up');
        }
        return $this->render('portal/login/sign-up-organization.html.twig', [
            'user' => $appUser,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/submit-sign-up", name="submit-sign-up", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param CommonHelper $commonHelper
     * @param SendSMS $objSendSMS
     * @param Mailer $mailer
     * @return Response
     */
    public function submitSignUp(Request $request, SessionInterface $session, CommonHelper $commonHelper, SendSMS
    $objSendSMS, Mailer $mailer): Response
    {
        $appUserId = $session->get('appUserId');
        if (!empty($appUserId)) {
            $entityManager = $this->getDoctrine()->getManager();
            $objAppUser = $entityManager->getRepository(AppUser::class)->findOneBy(["id" => $appUserId]);
            $objAppUserInfo = $objAppUser->getAppUserInfo();
            $userName = $objAppUser->getUserName();
            $userEmail = $objAppUserInfo->getUserEmail();
            $countryCode = $objAppUserInfo->getMobileCountryCode();
            $mobNumber = $objAppUserInfo->getUserMobileNumber();
            $userMobileNumber = $countryCode . $mobNumber;
            $strGenerateOTP = $commonHelper->generateNumericOTP(6);
            //save OTP to DB
            $entityManager = $this->getDoctrine()->getManager();

            //Check if OTP Already Exist for this user
            $arrTrnAuthOtpExt = $this->getDoctrine()->getRepository(TrnAuthOtp::class)->findBy(array('isValid' => 1,
                'mobileNumber' => $userMobileNumber, 'email' => $userEmail));
            if (!empty($arrTrnAuthOtpExt)) {
                foreach ($arrTrnAuthOtpExt as $objTrnAuthOtpExt) {
                    $objTrnAuthOtpExt->setIsValid(0);
                    $entityManager->persist($objTrnAuthOtpExt);
                    $entityManager->flush();
                }
            }
            //Check if OTP Already Exist for this user Ends

            $objTrnAuthOtp = new TrnAuthOtp();
            $objTrnAuthOtp->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
            $objTrnAuthOtp->setCreatedOn(new DateTime());
            $date = new DateTime();
            $date->modify("+2 minutes");
            $objTrnAuthOtp->setExpiredOn($date);
            $objTrnAuthOtp->setAppUser($objAppUser);
            $objTrnAuthOtp->setIsValid(1);
            $objTrnAuthOtp->setMobileNumber($userMobileNumber);
            $objTrnAuthOtp->setEmail($userEmail);
            $objTrnAuthOtp->setOtp($strGenerateOTP);
            $objTrnAuthOtp->setUserIpAddress($_SERVER['SERVER_ADDR']);
            $entityManager->persist($objTrnAuthOtp);
            $entityManager->flush();
            if (($countryCode == '91' || $countryCode == '+91') && false) {
                //Send OTP SMS
                $objSendSMS->sendSMS($countryCode . $mobNumber, $strGenerateOTP, 'loginMobileGenerateOTP');
                //Send OTP to Mobile Number
                $session->set('appUserId', $objAppUser->getId());
                return $this->redirectToRoute('verify-mobile-number');

            } else {
                //$userEmail = 'sgrkadam@gmail.com';
                //Send OTP to email Address
                $mailer->sendOTPMail($userEmail, $userName, $strGenerateOTP);
                //Send OTP to email Address
                return $this->redirectToRoute('verify-email-address');
            }
        }
        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/verify-mobile-number", name="verify-mobile-number", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param Mailer $mailer
     * @param SendSMS $objSendSMS
     * @return Response
     */
    public function verifyMobileNumber(Request $request, SessionInterface $session, Mailer $mailer, SendSMS $objSendSMS) : Response
    {
        $appUserId = $session->get('appUserId');
        if (!empty($appUserId)) {
            $entityManager = $this->getDoctrine()->getManager();
            $objAppUser = $entityManager->getRepository(AppUser::class)->findOneBy(["id" => $appUserId]);
            $objAppUserInfo =  $objAppUser->getAppUserInfo();
            $userName = $objAppUser->getUserName();
            $userEmail = $objAppUserInfo->getUserEmail();
            $countryCode = $objAppUserInfo->getMobileCountryCode();
            $mobNumber = $objAppUserInfo->getUserMobileNumber();
            $userMobileNumber = $countryCode.$mobNumber;
            if($request->isMethod('POST')) {
                $strOTPReceived = $request->get('mobileOTP');
                $objTrnAuthOtpExt = $this->getDoctrine()->getRepository(TrnAuthOtp::class)->findOneBy(array('isValid' => 1,
                    'mobileNumber' => $userMobileNumber, 'email' => $userEmail, 'otp' => $strOTPReceived));
                if (!empty($objTrnAuthOtpExt)) {
                    $objTrnAuthOtpExt->setIsValid(0);
                    $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
                    $objAppUser->setIsActive(1);
                    $objAppUser->setMstStatus($objMstStatus);
                    $entityManager->persist($objTrnAuthOtpExt);
                    $entityManager->persist($objAppUser);
                    $entityManager->flush();
                    //Send Successful User Creation Emails
                    $mailer->sendSuccessfulUserCreationMail($userEmail, $userName);

                    //Send Successful User Creation SMS
                    $objSendSMS->sendSMS($countryCode.$mobNumber,'Successfully Registered','loginMobileGenerateOTP');
                    return $this->redirectToRoute('verification-success');
                } else {
                    return $this->render('portal/login/mobile-number-verification.html.twig', array('notSuccessFul' =>
                        true, 'userMobileNumber' => $userMobileNumber));
                }
            }
            //return $this->redirectToRoute('verify-email-address');
            return $this->render('portal/login/mobile-number-verification.html.twig', array('notSuccessFul' =>
                null, 'userMobileNumber' => $userMobileNumber));
        }
        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/verify-email-address", name="verify-email-address", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param Mailer $mailer
     * @return Response
     */
    public function verifyEmailAddress(Request $request, SessionInterface $session, Mailer $mailer) : Response
    {
        $appUserId = $session->get('appUserId');
        if (!empty($appUserId)) {
            $entityManager = $this->getDoctrine()->getManager();
            $objAppUser = $entityManager->getRepository(AppUser::class)->findOneBy(["id" => $appUserId]);
            $objAppUserInfo =  $objAppUser->getAppUserInfo();
            $userName = $objAppUser->getUserName();
            $userEmail = $objAppUserInfo->getUserEmail();
            $countryCode = $objAppUserInfo->getMobileCountryCode();
            $mobNumber = $objAppUserInfo->getUserMobileNumber();
            $userMobileNumber = $countryCode.$mobNumber;
            if($request->isMethod('POST')) {
                $strOTPReceived = $request->get('emailOTP');
                $objTrnAuthOtpExt = $this->getDoctrine()->getRepository(TrnAuthOtp::class)->findOneBy(array('isValid' => 1,
                    'mobileNumber' => $userMobileNumber, 'email' => $userEmail, 'otp' => $strOTPReceived));
                if (!empty($objTrnAuthOtpExt)) {
                    $objTrnAuthOtpExt->setIsValid(0);
                    $objMstStatus = $entityManager->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
                    $objAppUser->setIsActive(1);
                    $objAppUser->setMstStatus($objMstStatus);
                    $entityManager->persist($objTrnAuthOtpExt);
                    $entityManager->persist($objAppUser);
                    $entityManager->flush();
                    //$userEmail = 'sgrkadam@gmail.com';
                    //Send Successful User Creation Emails
                    $mailer->sendSuccessfulUserCreationMail($userEmail, $userName);
                    return $this->redirectToRoute('verification-success');
                } else {
                    return $this->render('portal/login/email-address-verification.html.twig', array('notSuccessFul' =>
                        true, 'emailAddress' => $userEmail));
                }
            }
            return $this->render('portal/login/email-address-verification.html.twig', array('notSuccessFul' => null, 'emailAddress' => $userEmail ));
        }
        return $this->redirectToRoute('login-email');
    }

    /**
     * @Route("/verification-success", name="verification-success", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function verificationSuccess(Request $request, SessionInterface $session): Response
    {
        //Remove Session Variable
        $session->set('appUserId', null);
        return $this->render('portal/login/verification-success.html.twig');
    }

    /**
     * @Route("/resend-otp", name="resend-otp", methods={"GET", "POST"})
     * @param Request $request
     * @param SessionInterface $session
     * @param Mailer $mailer
     * @param SendSMS $objSendSMS
     * @param CommonHelper $commonHelper
     * @return Response
     */
    public function resendOTP(Request $request, SessionInterface $session, Mailer $mailer, SendSMS $objSendSMS, CommonHelper $commonHelper) {
        if($request->isMethod('POST') && $request->isXmlHttpRequest())  {
            $appUserId = $session->get('appUserId');
            if (!empty($appUserId)) {
                $strModule = $request->get('module');
                $entityManager = $this->getDoctrine()->getManager();
                $objAppUser = $entityManager->getRepository(AppUser::class)->findOneBy(["id" => $appUserId]);
                $objAppUserInfo = $objAppUser->getAppUserInfo();
                $userName = $objAppUser->getUserName();
                $userEmail = $objAppUserInfo->getUserEmail();
                $countryCode = $objAppUserInfo->getMobileCountryCode();
                $mobNumber = $objAppUserInfo->getUserMobileNumber();
                $userMobileNumber = $countryCode . $mobNumber;
                $strGenerateOTP = $commonHelper->generateNumericOTP(6);
                //save OTP to DB
                $entityManager = $this->getDoctrine()->getManager();

                //Check if OTP Already Exist for this user, reset the same
                $arrTrnAuthOtpExt = $this->getDoctrine()->getRepository(TrnAuthOtp::class)->findBy(array('isValid' => 1,
                    'mobileNumber' => $userMobileNumber, 'email' => $userEmail));
                if (!empty($arrTrnAuthOtpExt)) {
                    foreach ($arrTrnAuthOtpExt as $objTrnAuthOtpExt) {
                        $objTrnAuthOtpExt->setIsValid(0);
                        $entityManager->persist($objTrnAuthOtpExt);
                        $entityManager->flush();
                    }
                }
                //Check if OTP Already Exist for this user Ends

                $objTrnAuthOtp = new TrnAuthOtp();
                $objTrnAuthOtp->setOrgCompany($this->getDoctrine()->getRepository(OrgCompany::class)->find($this->getParameter('company_id')));
                $objTrnAuthOtp->setCreatedOn(new DateTime());
                $date = new DateTime();
                $date->modify("+2 minutes");
                $objTrnAuthOtp->setExpiredOn($date);
                $objTrnAuthOtp->setAppUser($objAppUser);
                $objTrnAuthOtp->setIsValid(1);
                $objTrnAuthOtp->setMobileNumber($userMobileNumber);
                $objTrnAuthOtp->setEmail($userEmail);
                $objTrnAuthOtp->setOtp($strGenerateOTP);
                $objTrnAuthOtp->setUserIpAddress($_SERVER['SERVER_ADDR']);
                $entityManager->persist($objTrnAuthOtp);
                $entityManager->flush();
                //If Country Code is of India Send SMS
                if ($countryCode == '91' || $countryCode == '+91') {
                    if (empty($strModule))
                        $strModule = "loginMobileGenerateOTP";
                    //Send OTP SMS
                    $objSendSMS->sendSMS($countryCode . $mobNumber, $strGenerateOTP, $strModule);
                    //Send OTP to Mobile Number
                    $session->set('appUserId', $objAppUser->getId());
                    return $this->json(array("otp_sent" => true, 'email' => false, 'mobile' => true));

                } else { //Else Send email.
                    //Send OTP to email Address
                    $mailer->sendOTPMail($userEmail, $userName, $strGenerateOTP);
                    //Send OTP to email Address
                    return $this->json(array("otp_sent" => true, 'email' => true, 'mobile' => false));
                }
            }
        }
        return $this->json(array("otp_sent" => false));
    }
}