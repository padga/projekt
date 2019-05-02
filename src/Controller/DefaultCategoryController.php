<?php
/**
 * DefaultCategory controller.
 */

namespace App\Controller;

use App\Entity\DefaultCategory;
use App\Repository\DefaultCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CategoryType;

/**
 * Class DefaultCategoryController.
 *
 * @Route("/category")
 */
class DefaultCategoryController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param DefaultCategoryRepository                 $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="category_index",
     * )
     */
    public function index(Request $request, DefaultCategoryRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * View action.
     *
     * @param DefaultCategory $category
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="category_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(DefaultCategory $category): Response
    {
        return $this->render(
            'category/view.html.twig',
            ['category' => $category]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\DefaultCategoryRepository $repository Category repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     *
     * @Route(
     *     "/new",
     *     methods={"GET", "POST"},
     *     name="category_new",
     * )
     */
    public function new(Request $request, DefaultCategoryRepository $repository): Response
    {
        $category = new DefaultCategory();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());
            $repository->save($category);

            $this->addFlash('success', 'message.category_created_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Entity\DefaultCategory               $category   Category entity
     * @param \App\Repository\DefaultCategoryRepository $repository Category repository
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
     *     name="category_edit",
     * )
     */
    public function edit(Request $request, DefaultCategory $category, DefaultCategoryRepository $repository): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'put']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUpdatedAt(new \DateTime());
            $repository->save($category);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
