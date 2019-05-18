<?php
/**
 * Transaction controller.
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
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
//            $transaction->getTag();
            $repository->save($transaction);

            $this->addFlash('success', 'message.created_successfully');

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'transaction/new.html.twig',
            ['form' => $form->createView()]
        );
    }
}
