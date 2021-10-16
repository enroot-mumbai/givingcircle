<?php

namespace App\Controller\User;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Entity\SystemApp\AppUserInfo;
use App\Form\User\UserPasswordType;
use App\Form\User\UserProfileType;
use App\Service\FileUploaderHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_APP_USER")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/core/user", name="user_index", methods={"GET","POST"})
     */
    public function index()
    {
        $user = $this->getUser();
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);


    }

    /**
     * @Route("/core/user/user_profile", name="user_profile", methods={"GET","POST"})
     * @param Request $request
     * @param FileUploaderHelper $fileUploaderHelper
     * @return Response
     * @throws \Exception
     */
    public function edit(Request $request, FileUploaderHelper $fileUploaderHelper): Response
    {
        $user_id = $this->getUser()->getId();
        $appUserInfo = $this->getDoctrine()->getRepository(AppUserInfo::class)->findOneBy(['appUser' => $user_id]);
        $form = $this->createForm(UserProfileType::class, $appUserInfo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $file = $form['userAvatarImage']->getData();
            if ($file) {
                if ($appUserInfo->getUserAvatarImage() != '') {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $appUserInfo->getUserAvatarImage());
                } else {
                    $newFilename = $fileUploaderHelper->uploadPrivateFile($file, $avatarImage = null);
                }
                $appUserInfo->setUserAvatarImage($newFilename);
                $appUserInfo->setUserAvatarImagePath($this->getParameter('user_file_folder'));
            }
            $entityManager->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/user_profile.html.twig', [
            'form' => $form->createView(),
            'label_title' => 'label.my_profile',
            'label_button' => 'label.update',
        ]);
    }

    /**
     * @Route("/portal/myaccount/changepassword", name="portal_myaccount_changepassword", methods={"GET","POST"})
     * @Route("/core/user/user_change_password", name="user_change_password", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */


    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUserPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('logout');
        }
        return $this->render('user/user_change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
