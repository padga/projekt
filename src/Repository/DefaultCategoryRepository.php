<?php
/**
 *  DefaultCategory Repository.
 */

namespace App\Repository;

use App\Entity\DefaultCategory;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DefaultCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DefaultCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DefaultCategory[]    findAll()
 * @method DefaultCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DefaultCategoryRepository extends ServiceEntityRepository
{
    /**
     * DefaultCategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DefaultCategory::class);
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
     * Save record.
     *
     * @param \App\Entity\DefaultCategory $category Category entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(DefaultCategory $category): void
    {
        $this->_em->persist($category);
        $this->_em->flush($category);
    }

    /**
     * Delete record.
     *
     * @param \App\Entity\DefaultCategory $category Category entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(DefaultCategory $category): void
    {
        $this->_em->remove($category);
        $this->_em->flush($category);
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
