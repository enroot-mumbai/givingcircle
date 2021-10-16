<?php

namespace App\Controller\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\SystemApp\AppUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/core" , name="core_")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class WelcomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="welcome")
     * @param AppUserRepository $appUsersRepository
     * @return RedirectResponse
     * @throws \Exception
     */
    public function redirectUser(AppUserRepository $appUsersRepository)
    {
       /*
        * Set logged in user loggedin date and time
        */
           $user = $this->getUser();
           $user->setUserLastLogin(new \DateTime());
           $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('core_home');
    }
    /**
     * @Route("/home", name="home")
     * @Route("/system", name="system")
     * @Route("/master", name="master")
     * @Route("/user", name="user")
     * @Route("/organization", name="organization")
     * @Route("/cms", name="cms")
     * @Route("/crm", name="crm")
     * @Route("/form", name="form")
     * @Route("/product", name="product")
     * @Route("/registration", name="registration")
      */

    public function index()
    {

       return $this->render('layout/index.html.twig');
    }
}
