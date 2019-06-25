<?php
/**
 * Admin controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeEmailType;
use App\Form\ChangePasswordType;
use App\Form\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AdminController.
 *
 * @Route("/admin")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * Users index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\UserRepository            $repository User repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/users",
     *     name="users_index",
     * )
     */
    public function indexUsers(Request $request, UserRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'admin/users_index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request         HTTP request
     * @param \App\Repository\UserRepository            $repository      User repository
     * @param UserPasswordEncoderInterface              $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     *
     * @Route(
     *     "/new_user",
     *     methods={"GET", "POST"},
     *     name="admin_register",
     * )
     *
     *
     */
    public function new(Request $request, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $password = $user->getPassword();
            $user->setRoles(['ROLE_USER']);
            $password = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($password);
            $repository->save($user);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('users_index');
        }

        return $this->render(
            'admin/new_user.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Admin panel.
     *
     * * @Route(
     *     "/",
     *     name="admin_panel",
     *)
     *@IsGranted(
     *     "ROLE_ADMIN",
     *
     * )
     *
     * @return Response
     */
    public function index()
    {
        return $this->render(
            'admin/index.html.twig'
        );
    }

    /**
     * Manage users.
     *
     * * @Route(
     *     "/manage/{id}",
     *     name="admin_manage",
     *     requirements={"id": "[1-9]\d*"}
     *)
     * @IsGranted(
     *     "ROLE_ADMIN",
     *
     * )
     *
     * @param User $user
     *
     * @return Response
     */
    public function manage(User $user)
    {
        return $this->render(
            'admin/manage.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Change password.
     *
     * * @Route(
     *     "/email/{id}",
     *     name="email_edit",
     *     requirements={"id": "[1-9]\d*"}
     *)
     * @IsGranted(
     *     "ROLE_ADMIN"
     * )
     *
     * @param User           $user
     * @param Request        $request
     * @param UserRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeEmail(User $user, Request $request, UserRepository $repository)
    {
        $form = $this->createForm(ChangeEmailType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($user);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('users_index');
        }

        return $this->render(
            'admin/changeemail.html.twig',
            [
                    'form' => $form->createView(),
                    'user' => $user,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request         HTTP request
     * @param User                                      $user
     * @param \App\Repository\UserRepository            $repository      User repository     *
     * @param UserPasswordEncoderInterface              $passwordEncoder
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="password_change",
     * )
     *
     *      *     * @IsGranted(
     *     "MANAGE",
     *     subject="user",)
     */
    public function edit(Request $request, User $user, UserRepository $repository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangePasswordType::class, $user, ['method' => 'put']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
            $password = $user->getPassword();
            $password = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($password);
            $repository->save($user);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render(
            'admin/changepassword.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Edit role.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\User                          $user       User entity
     * @param \App\Repository\UserRepository            $repository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/change_role",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="role_edit",
     * )
     */
    public function editRole(Request $request, User $user, UserRepository $repository): Response
    {
        if ($user->getId() !== $this->getUser()->getId()) {
            $form = $this->createForm(FormType::class, $user, ['method' => 'PUT']);
            $form->handleRequest($request);

            if (1 == count($user->getRoles())) {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $repository->save($user);

                $this->addFlash('success', 'message.updated_successfully');

                return $this->redirectToRoute('users_index');
            }

            return $this->render(
                'admin/edit_role.html.twig',
                [
                    'form' => $form->createView(),
                    'user' => $user,
                ]
            );
        } else {
            $this->addFlash('warning', 'message.item_not_found');

            return $this->redirectToRoute('users_index');
        }
    }
}
