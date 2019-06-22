<?php
/**
 * Transaction Repository.
 */

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    /**
     * CategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('t.updatedAt', 'DESC');
    }

    /**
     * Query tasks by author.
     *
     * @param \App\Entity\User|null $user User entity
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     *
     * @throws \Exception
     */
    public function queryByAuthor(User $user = null): QueryBuilder
    {
        $queryBuilder = $this->queryAll();
        $time = new \DateTime('now');
        $time->modify('-1 month');
        if (!is_null($user)) {
            $queryBuilder->andWhere('t.owner = :owner')
                ->setParameter('owner', $user)
                ->andWhere('t.createdAt > :time')
                ->setParameter('time', $time);
        }

        return $queryBuilder;
    }

    /**
     * Save record.
     *
     * @param \App\Entity\Transaction $tag Transaction entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Transaction $tag): void
    {
        $this->_em->persist($tag);
        $this->_em->flush($tag);
    }

    /**
     * Delete record.
     *
     * @param \App\Entity\Transaction $tag Transaction entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Transaction $tag): void
    {
        $this->_em->remove($tag);
        $this->_em->flush($tag);
    }

    /**
     * Gets income of a User.
     *
     * @param User $user
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function queryIncome(User $user): QueryBuilder
    {
        $time = new \DateTime('now');
        $time->modify('-1 month');
        $queryBuilder = $this->queryByAuthor($user);
        if (!is_null($user)) {
            $queryBuilder->andWhere('t.type = :type')
                ->setParameter('type', 2)
                ->andWhere('t.createdAt > :time')
                ->setParameter('time', $time);
        }

        return $queryBuilder;
    }

    /**
     * Query monthly income.
     *
     * @param User $user
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function countIncome(User $user)
    {
        $queryBuilder = $this->queryIncome($user);

        return $queryBuilder->select('SUM(t.amount) AS summary')
                ->getQuery()
                ->getSingleScalarResult();
    }

    /**
     * Counts expenses.
     * @param User $user
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function countExpense(User $user)
    {
        $queryBuilder = $this->queryExpense($user);

        return $queryBuilder->select('SUM(t.amount) AS summary')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Gets expense of a User.
     *
     * @param User $user
     *
     * @return QueryBuilder
     *
     * @throws \Exception
     */
    public function queryExpense(User $user): QueryBuilder
    {
        $time = new \DateTime('now');
        $time->modify('-1 month');
        $queryBuilder = $this->queryByAuthor($user);
        if (!is_null($user)) {
            $queryBuilder->andWhere('t.type = :type')
                ->setParameter('type', 1)
            ->andWhere('t.createdAt > :time')
                ->setParameter('time', $time);
        }

        return $queryBuilder;
    }

    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?: $this->createQueryBuilder('t');
    }
}
