<?php
/**
 * Transaction controller.
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Transaction Controller.
 *
 * @Route("/transaction")
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
     * @param TransactionRepository $repository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function new(Request $request, TransactionRepository $repository): Response
    {
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

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render(
            'transaction/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
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
     *
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
//        $amount = $repository->countIncomeByTag($user, $categoryArray);

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
     *
     *      * @IsGranted(
     *     "MANAGE",
     *     subject="transaction",
     * )
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
}
