<?php
/**
 * Type controller.
 */

namespace App\Controller;

use App\Form\TypeType;
use App\Repository\TypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Type;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagController.
 *
 * @Route("/type")
 *
 * @IsGranted("ROLE_ADMIN")
 */
class TypeController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param TypeRepository                            $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/types",
     *     name="type_index",
     * )
     */
    public function index(Request $request, TypeRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryAll(),
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'type/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\TypeRepository            $repository Category repository
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
     *     name="type_new",
     * )
     */
    public function new(Request $request, TypeRepository $repository): Response
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setCreatedAt(new \DateTime());
            $type->setUpdatedAt(new \DateTime());
            $repository->save($type);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('type_index');
        }

        return $this->render(
            'type/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param Type                                      $type
     * @param TypeRepository                            $repository Category repository
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
     *     name="type_edit",
     * )
     */
    public function edit(Request $request, Type $type, TypeRepository $repository): Response
    {

        $form = $this->createForm(TypeType::class, $type, ['method' => 'put']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type->setUpdatedAt(new \DateTime());
            $repository->save($type);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('type_index');
        }

        return $this->render(
            'type/edit.html.twig',
            [
                'form' => $form->createView(),
                'type' => $type,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param Type                                      $type
     * @param TypeRepository                            $repository Tag repository
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
     *     name="type_delete",
     * )
     *
     */
    public function delete(Request $request, Type $type, TypeRepository $repository): Response
    {
        if ($type->getTransactions()->count()) {
            $this->addFlash('warning', 'message.type_contains_transactions');

            return $this->redirectToRoute('type_index');
        }
        $form = $this->createForm(FormType::class, $type, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE')) {
            $repository->delete($type);

            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('type_index');
        }

        return $this->render(
            'type/delete.html.twig',
            [
                'form' => $form->createView(),
                'type' => $type,
            ]
        );
    }
}
