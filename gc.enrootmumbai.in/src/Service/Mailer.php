<?php

namespace App\Service;

use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;

    private $noReplyEmail;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->noReplyEmail = 'givingcircle@givingcircle.in';
    }
    // Send Forgot Password Email to User
    public function mailerForgotPassword($userFullName, $userEmail, $token)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject('Reset your password')
            ->htmlTemplate('mailer/security/forgotpassword.html.twig')
            ->context([
                'validation_key' => $token,
                'expiration_date' => new DateTime('+24 hours'),
                'userFullName' => $userFullName
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

    }
    // Send Create Password Email to User triggered from Organization User Interface
    public function mailerCreatePassword($userEmail, $userName, $token)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject('Create your password')
            ->htmlTemplate('mailer/security/createpassword.html.twig')
            ->context([
                'userName' => $userName,
                'validation_key' => $token,
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
    // Send Reset Password Email to User
    public function mailerResetPassword($userEmail, $userName, $token)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject('Reset your password')
            ->htmlTemplate('mailer/security/createpassword.html.twig')
            ->context([
                'userName' => $userName,
                'validation_key' => $token,
                'expiration_date' => new DateTime('+24 hours')
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

    }

    /**
     * @param $userEmail
     * @param $userName
     * @param $strGenerateOTP
     */
    public function sendOTPMail($userEmail, $userName, $strGenerateOTP) {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject('Email Verification')
            ->htmlTemplate('mailer/security/sendotp.html.twig')
            ->context([
                'userName' => $userName,
                'validation_key' => $strGenerateOTP,
                'expiration_date' => new DateTime('+24 hours')
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $userName
     */
    public function sendSuccessfulUserCreationMail($userEmail, $userName){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject('Email Verification')
            ->htmlTemplate('mailer/security/verification_successful.html.twig')
            ->context([
                'userName' => $userName,
                'expiration_date' => new DateTime('+24 hours')
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $userName
     */
    public function sendSuccessfulProjectCreationUserMail($userEmail, $userName, $projectName, $gcContactEmail){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject($userName. " new Project ". $projectName. " created")
            ->htmlTemplate('mailer/project/createprojectsuccessuser.html.twig')
            ->context([
                'userName' => $userName,
                'gcContactEmail' => $gcContactEmail
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $userName
     */
    public function sendSuccessfulProjectCreationGCMail($gcEmail, $userName, $projectName, $projectLink){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($gcEmail)
            ->subject($userName. " new Project ". $projectName. " created")
            ->htmlTemplate('mailer/project/createprojectsuccessgc.html.twig')
            ->context([
                'userName' => $userName,
                'projectLink' => $projectLink
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }


    /**
     * @param $emailAddress
     * @param $userName
     * @param $amount
     * @param $eventName
     * @param $projectName
     */
    public function sendSuccessfulPayment($emailAddress, $userName, $amount, $eventName, $projectName) {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($emailAddress)
            ->subject("Event $eventName Thank for Donation")
            ->htmlTemplate('mailer/payment/payment-successful.html.twig')
            ->context([
                'userName' => $userName,
                'amount' => $amount,
                'projectName' => $projectName
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $userName
     * @param $eventName
     */
    public function sendSuccessfulEventCreationUserMail($userEmail, $userName, $eventName){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject($userName. " new Project ". $eventName. " created")
            ->htmlTemplate('mailer/event/createeventsuccessuser.html.twig')
            ->context([
                'userName' => $userName,
//                'gcContactEmail' => $gcContactEmail
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $emailAddress
     * @param $userName
     * @param $amount
     * @param $eventName
     * @param $projectName
     * @param $certificateNo
     */
    public function sendSuccessfulPayment80G($emailAddress, $userName, $amount, $eventName, $projectName, $certificateNo)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($emailAddress)
            ->subject("Event $eventName 80G Certificate $certificateNo")
            ->htmlTemplate('mailer/payment/80-g-receipt.html')
            ->context([
                'userName' => $userName,
                'amount' => $amount,
                'projectName' => $projectName
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
    /*
     * @param $gcEmail
     * @param $userName
     * @param $eventName
     */
    public function sendSuccessfulEventCreationGCMail($gcEmail, $userName, $eventName){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($gcEmail)
            ->subject($userName. " Event ". $eventName. " created")
            ->htmlTemplate('mailer/event/createeventsuccessgc.html.twig')
            ->context([
//                'userName' => $userName,
//                'projectLink' => $projectLink
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $projectName
     * @param $userName
     * @param $projectLink
     * @param $socialMediaWidget
     */
    public function sendSuccessfulProjectRequestToJoinAccepted($userEmail, $projectName, $userName, $projectLink, $socialMediaWidget) {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("$projectName Request to Join by $userName Accepted")
            ->htmlTemplate('mailer/project/projectRequestToJoinAccepted.html.twig')
            ->context([
                'userEmail' => $userEmail,
                'projectName' => $projectName,
                'projectLink' => $projectLink,
                'socialMediaWidget' => $socialMediaWidget,
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $projectName
     * @param $userName
     * @param $projectLink
     * @param $socialMediaWidget
     */
    public function sendSuccessfulProjectRequestToJoinRejected($userEmail, $projectName, $userName, $projectLink, $socialMediaWidget)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("$projectName Request to Join by $userName Rejected")
            ->htmlTemplate('mailer/project/projectRequestToJoinRejected.html.twig')
            ->context([
                'userEmail' => $userEmail,
                'projectName' => $projectName,
                'projectLink' => $projectLink,
                'socialMediaWidget' => $socialMediaWidget,
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $eventName
     * @param $userName
     * @param $eventDeactivationMessage
     */
    public function sendSuccessfulEventDeactivation($userEmail, $eventName, $userName, $eventDeactivationMessage)
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("Event $eventName Deactivated")
            ->htmlTemplate('mailer/event/successfulEventDeactivated.html.twig')
            ->context([
                'eventName' => $eventName,
                'userName' => $userName,
                'eventDeactivationMessage' => $eventDeactivationMessage
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $eventName
     * @param $userName
     * @param $date
     * @param $time
     */
    public function sendEventReminder($userEmail, $eventName, $userName, $date, $time){
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("Event $eventName Reminder to attend Event")
            ->htmlTemplate('mailer/event/eventReminder.html.twig')
            ->context([
                'eventName' => $eventName,
                'userName'  => $userName,
                'date'      => $date,
                'time'      => $time
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $eventName
     * @param $userName
     * @param $changeMakerName
     * @param $projectName
     * @param $eventId
     * @param $newFilename
     */
    public function sendEventBroadcastUpdate($userEmail, $eventName, $userName, $changeMakerName, $projectName,
                                             $eventId, $newFilename = "") {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("Update on $changeMakerName of $projectName")
            ->htmlTemplate('mailer/event/eventBroadcast.html.twig')
            //->attachFromPath($newFilename, 'Image')
            ->context([
                'eventName' => $eventName,
                'userName'  => $userName,
                'changeMakerName'  => $changeMakerName,
                'projectName'  => $projectName,
                'eventId'  => $eventId,
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $eventName
     * @param $userName
     * @param $changeMakerName
     * @param $projectName
     * @param $eventId
     * @param string $newFilename
     * @param string $strBroadCastMessage
     */
    public function sendEventBroadcastUpdateCF($userEmail, $eventName, $userName, $changeMakerName, $projectName,
                                             $eventId, $newFilename = "", $strBroadCastMessage = "") {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("Update on $changeMakerName of $projectName")
            ->htmlTemplate('mailer/event/eventBroadcastCF.html.twig')
            //->attachFromPath($newFilename, 'Image')
            ->context([
                'eventName' => $eventName,
                'userName'  => $userName,
                'changeMakerName'  => $changeMakerName,
                'projectName'  => $projectName,
                'eventId'  => $eventId,
                'strBroadCastMessage' => $strBroadCastMessage
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    /**
     * @param $userEmail
     * @param $userName
     */
    public function sendSubscribedEmail($userEmail, $userName )
    {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("New Subscription")
            ->htmlTemplate('mailer/general/subscription.html.twig')
            ->context([
                'userName'  => $userName
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function sendInviteToJoin($userEmail, $projectName, $userName, $projectLink, $changeMakerName) {
        $email = (new TemplatedEmail())
            ->from($this->noReplyEmail)
            ->to($userEmail)
            ->subject("Invitation to Join")
            ->htmlTemplate('mailer/general/invite-to-join.html.twig')
            ->context([
                'userName'  => $userName,
                'changeMakerName'  => $changeMakerName,
                'projectName'  => $projectName,
                'projectLink'  => $projectLink,
            ]);
        try {
            $obj = $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }
    }
}