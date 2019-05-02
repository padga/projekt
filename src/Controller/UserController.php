<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Form\RegisterType;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Index action.
     *
     * @Route(
     *     "/"
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


}
