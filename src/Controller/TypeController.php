<?php
/**
 * Type controller.
 */

namespace App\Controller;

use App\Form\TypeType;
use App\Repository\TypeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Type;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagController.
 *
 * @Route("/type")
 */
class TypeController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param TypeRepository                             $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
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
     * View action.
     *
     * @param Type $tag
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     name="type_view",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function view(Type $tag): Response
    {
        return $this->render(
            'tag/view.html.twig',
            ['tag' => $tag]
        );
    }

    /**
     * New action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\TypeRepository             $repository Category repository
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

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'type/new.html.twig',
            ['form' => $form->createView()]
        );
    }

//    /**
//     * Edit action.
//     *
//     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
//     * @param \App\Entity\Tag                           $tag        Tag entity
//     * @param \App\Repository\TagRepository             $repository Category repository
//     *
//     * @return \Symfony\Component\HttpFoundation\Response HTTP response
//     *
//     * @throws \Doctrine\ORM\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     * @throws \Exception
//     *
//     * @Route(
//     *     "/{id}/edit",
//     *     methods={"GET", "PUT"},
//     *     requirements={"id": "[1-9]\d*"},
//     *     name="tag_edit",
//     * )
//     */
//    public function edit(Request $request, Tag $tag, TagRepository $repository): Response
//    {
//        $form = $this->createForm(TagType::class, $tag, ['method' => 'put']);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $tag->setUpdatedAt(new \DateTime());
//            $repository->save($tag);
//
//            $this->addFlash('success', 'message.updated_successfully');
//
//            return $this->redirectToRoute('tag_index');
//        }
//
//        return $this->render(
//            'tag/edit.html.twig',
//            [
//                'form' => $form->createView(),
//                'tag' => $tag,
//            ]
//        );
//    }
//
//    /**
//     * Delete action.
//     *
//     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
//     * @param \App\Entity\Tag                      $tag   Tag entity
//     * @param \App\Repository\TagRepository        $repository Tag repository
//     *
//     * @return \Symfony\Component\HttpFoundation\Response HTTP response
//     *
//     * @throws \Doctrine\ORM\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     *
//     * @Route(
//     *     "/{id}/delete",
//     *     methods={"GET", "DELETE"},
//     *     requirements={"id": "[1-9]\d*"},
//     *     name="tag_delete",
//     * )
//     */
//    public function delete(Request $request, Tag $tag, TagRepository $repository): Response
//    {
////        if ($tag->getTasks()->count()) {
////            $this->addFlash('warning', 'message.category_contains_tasks');
////
////            return $this->redirectToRoute('category_index');
////        }
//
//        $form = $this->createForm(TagType::class, $tag, ['method' => 'DELETE']);
//        $form->handleRequest($request);
//
//        if ($request->isMethod('DELETE')) {
//            $repository->delete($tag);
//
//            $this->addFlash('success', 'message.deleted_successfully');
//
//            return $this->redirectToRoute('tag_index');
//        }
//
//        return $this->render(
//            'tag/delete.html.twig',
//            [
//                'form' => $form->createView(),
//                'tag' => $tag,
//            ]
//        );
//    }
}
