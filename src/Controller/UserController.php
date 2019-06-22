<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegisterType;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class UserController.
 *
 * @Route("/")
 */
class UserController extends AbstractController
{
    /**
     * Index action.
     *
     * @Route(
     *     "/", name="dashboard"
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
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
     *     "/register",
     *     methods={"GET", "POST"},
     *     name="auth_register",
     * )
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

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'auth/register.html.twig',
            ['form' => $form->createView()]
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
     *     name="user_edit",
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
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param User                                      $user
     * @param UserRepository                            $repository Tag repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_delete",
     * )
     */
    public function delete(Request $request, User $user, UserRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $user, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE')) {
            $repository->delete($user);

            $session = new Session();
            $session->invalidate();

            $this->addFlash('success', 'message.deleted_successfully');


            return $this->redirectToRoute('dashboard');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * View action.
     *
     *
     * @param TransactionRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Route(
     *     "/dashboard",
     *     name="user_dashboard",
     * )
     */
    public function view(TransactionRepository $repository): Response
    {
        $expense = $repository->countExpense($this->getUser());
        $income = $repository->countIncome($this->getUser());

        return $this->render(
            'user/index.html.twig',
            ['expense' => $expense,
                'income' => $income,
            ]
        );
    }
}
