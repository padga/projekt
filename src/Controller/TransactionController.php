<?php
/**
 * Transaction controller.
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TagRepository;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Transaction Controller.
 *
 * @Route("/transaction")
 *
 * @IsGranted("ROLE_USER")
 */
class TransactionController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param TransactionRepository                     $repository Repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator  Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Exception
     *
     * @Route(
     *     "/",
     *     name="transaction_index",
     * )
     */
    public function index(Request $request, TransactionRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $repository->queryByAuthor($this->getUser()),
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'transaction/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * new action.
     *
     *
     * @Route(
     *     "/add",
     *     name="transaction_add",
     * )
     *
     * @param Request               $request
     * @param TagRepository         $tagRepository
     * @param TransactionRepository $repository
     * @param PaginatorInterface    $paginator
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function new(Request $request, TagRepository $tagRepository, TransactionRepository $repository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $tagRepository->queryByAuthor($this->getUser()),
            $request->query->getInt('page', 1)
        );

        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction, array(
            'user' => $this->getUser(),
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setCreatedAt(new \DateTime());
            $transaction->setUpdatedAt(new \DateTime());
            $transaction->setOwner($this->getUser());
            $repository->save($transaction);

            $this->addFlash('success', 'message.transaction_created_successfully');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render(
            'transaction/new.html.twig',
            ['form' => $form->createView(),
                'pagination' => $pagination,
                ]
        );
    }

    /**
     * Show incomes.
     *
     * @Route(
     *     "/incomes",
     *     name="transaction_incomes",
     * )
     *
     * @param TransactionRepository $repository
     * @param PaginatorInterface    $paginator
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function showIncomes(TransactionRepository $repository, PaginatorInterface $paginator): Response
    {
        $categoryArray = [];
        $amountArray = [];
        $user = $this->getUser();
        $pagination = $paginator->paginate(
            $repository->queryIncome($user)
        );
        $tag = $repository->queryByAuthor($user);
        $summary = $repository->countIncome($user);

        return $this->render(
            'transaction/incomes.html.twig',
            ['pagination' => $pagination,
               'tag' => $tag,
                'summary' => $summary,
               'amountArray' => $amountArray,
                'categoryArray' => $categoryArray,
                ]
        );
    }

    /**
     * Show expenses.
     *
     * @Route(
     *     "/expenses",
     *     name="transaction_expenses",
     * )
     *
     * @param TransactionRepository $repository
     * @param PaginatorInterface    $paginator
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function showExpenses(TransactionRepository $repository, PaginatorInterface $paginator): Response
    {
        $categoryArray = [];
        $amountArray = [];
        $user = $this->getUser();
        $pagination = $paginator->paginate(
            $repository->queryExpense($user)
        );
        $tag = $repository->queryByAuthor($user);
        $summary = $repository->countExpense($user);

        return $this->render(
            'transaction/expenses.html.twig',
            ['pagination' => $pagination,
                'tag' => $tag,
                'summary' => $summary,
                'amountArray' => $amountArray,
                'categoryArray' => $categoryArray,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request     HTTP request
     * @param Transaction                               $transaction
     * @param TransactionRepository                     $repository  Category repository
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
     *     name="transaction_edit",
     * )
     *
     * @IsGranted(
     *     "MANAGE",
     *     subject="transaction",
     * )
     */
    public function edit(Request $request, Transaction $transaction, TransactionRepository $repository): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction, array(
            'user' => $this->getUser(),
            'method' => 'put',
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transaction->setUpdatedAt(new \DateTime());
            $repository->save($transaction);

            $this->addFlash('success', 'message.updated_successfully');

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render(
            'transaction/edit.html.twig',
            [
                'form' => $form->createView(),
                'transaction' => $transaction,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request     HTTP request
     * @param Transaction                               $transaction
     * @param TransactionRepository                     $repository  Tag repository
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
     *     name="transaction_delete",
     * )
     *
     *     * @IsGranted(
     *     "MANAGE",
     *     subject="transaction",
     * )
     */
    public function delete(Request $request, Transaction $transaction, TransactionRepository $repository): Response
    {
        $form = $this->createForm(FormType::class, $transaction, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE')) {
            $repository->delete($transaction);

            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render(
            'transaction/delete.html.twig',
            [
                'form' => $form->createView(),
                'transaction' => $transaction,
            ]
        );
    }
}
