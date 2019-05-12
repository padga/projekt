<?php
/**
 * Admin controller.
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class AdminController.
 *
 * @Route("/admin")
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
    public function index(Request $request, UserRepository $repository, PaginatorInterface $paginator): Response
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
     * @param User $user
     *
     * @return Response
     *                  * @Route(
     *                  "/user/{id}",
     *                  name="admin_view",
     *                  requirements={"id": "[1-9]\d*"},
     *                  )
     */
    public function view(User $user): Response
    {
        return $this->render(
            'admin/view.html.twig',
            ['user' => $user]
        );
    }
}
