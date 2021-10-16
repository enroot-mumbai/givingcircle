<?php

namespace App\Controller\Organization;

use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserInfo;
use App\Form\Organization\OrgCompanyUserResetPasswordType;
use App\Form\Organization\OrgCompanyUserType;
use App\Repository\SystemApp\AppUserInfoRepository;
use App\Repository\SystemApp\AppUserRepository;
use App\Service\Mailer;
use DateTime;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/core/organization/user", name="org_company_user_")
 * @IsGranted("ROLE_SYS_MODULE_ADMIN")
 */
class OrgCompanyUserController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppUserRepository $appUserRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(AppUserRepository $appUserRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_index');
            }
        $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($company_id);
        $queryBuilder = $appUserRepository->getUserByCompanyId($company_id);
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('organization/org_company_user/index.html.twig', [
            'org_company_users' => $pagination,
            'org_company' => $orgCompany,
            'path_index' => 'org_company_user_index',
            'path_add' => 'org_company_user_add',
            'path_edit' => 'org_company_user_edit',
            'path_show' => 'org_company_user_show',
            'label_title' => 'label.company_user',
        ]);
    }
    /**
     * @Route("/add", name="add", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, Mailer $mailer): Response
    {
        $appUser = new AppUser();
        $orgCompany = $this->getDoctrine()->getRepository(OrgCompany::class)->find($request->query->get('company_id'));
        $appUser->setAppUserInfo(new AppUserInfo())->getAppUserInfo()->setOrgCompany($orgCompany);
        $form = $this->createForm(OrgCompanyUserType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $userEmail = $appUser->getAppUserInfo()->getUserEmail();
            $userName = $appUser->getUserName();
            $token = bin2hex(random_bytes(32));
            $appUser->setRowId(Uuid::uuid4()->toString());
            $appUser->setUserCreationToken($token);
            $appUser->setUserRole($_POST['org_company_user']['userRole']);
            $generatePassword = random_bytes(10);
            $appUser->setUserPassword($encoder->encodePassword($appUser, $generatePassword));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appUser);
            $entityManager->flush();
            // Send Reset Email to User
            $mailer->mailerCreatePassword($userEmail, $userName, $token);
            $this->addFlash('success', 'form.created_successfully');
            return $this->redirectToRoute('org_company_user_index', $request->query->all());
        }
        return $this->render('organization/org_company_user/form.html.twig', [
            'org_company_user' => $appUser,
            'form' => $form->createView(),
            'index_path' => 'org_user_index',
            'label_title' => 'label.user',
            'label_button' => 'label.create',
            'mode' => 'add'
        ]);
    }
    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppUser $appUser
     * @return Response
     */
    public function edit(Request $request, AppUser $appUser): Response
    {
        $form = $this->createForm(OrgCompanyuserType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('org_company_user_index', $request->query->all());
        }
        return $this->render('organization/org_company_user/form.html.twig', [
            'org_company_user' => $appUser,
            'form' => $form->createView(),
            'index_path' => 'org_user_index',
            'label_title' => 'label.user',
            'label_button' => 'label.update',
            'mode' => 'edit'
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET","POST"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        // If company_id param is entry redirect to Company listing
        $company_id = $request->query->get('company_id');
        if(!$company_id) {
            return $this->redirectToRoute('org_company_user_index');
        }
        $appUser = $this->getDoctrine()->getRepository(AppUser::class)->find($id);
        if (!$appUser) {
            throw $this->createNotFoundException('No Data found for id '.$id);
        }

        return $this->render('organization/org_company_user/show.html.twig', [
            'data' => $appUser,
            'label_title' => 'label.company_user',
            'label_button' => 'label.update',
            'path_index' => 'org_company_user_index',
            'path_edit' => 'org_company_user_edit',
            'path_delete' => 'org_company_user_delete',
        ]);
    }


    /**
     * @Route("/resetpassword/{id}", name="resetpassword", methods={"GET","POST"})
     * @param Request $request
     * @param AppUser $appUser
     * @param Mailer $mailer
     * @return Response
     * @throws Exception
     */
    public function resetpassword(Request $request, AppUser $appUser, Mailer $mailer): Response
    {
        $form = $this->createForm(OrgCompanyUserResetPasswordType::class, $appUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = bin2hex(random_bytes(32));
            $tokenExpiryTime = new DateTime('+24 hour');
            $userName = $appUser->getUserName();
            $userEmail = $appUser->getAppUserInfo()->getUserEmail();
            $appUser->setUserResetPasswordToken($token);
            $appUser->setUserResetPasswordTokenExpiry($tokenExpiryTime);
            $this->getDoctrine()->getManager()->flush();
            // Send Reset Email to User
            $mailer->mailerResetPassword($userEmail, $userName, $token);
            $this->addFlash('success', 'form.updated_successfully');
            return $this->redirectToRoute('org_company_user_index', $request->query->all());
        }
        return $this->render('form/form.html.twig', [
            'org_company_user' => $appUser,
            'form' => $form->createView(),
            'index_path' => 'org_user_index',
            'label_title' => 'label.reset_password',
            'label_button' => 'action.reset_password'
        ]);
    }
    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param AppUser $appUser
     * @return Response
     */
    public function delete(Request $request, AppUser $appUser): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('org_company_user_index', $request->query->all());
    }
}
