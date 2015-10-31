<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\Query\ProductCategoryQuery;
use Furniture\ProductBundle\Entity\Category;

class ProductCategoryRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Find all categories
     *
     * @return Category[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Category::class, 'c')
            ->select('c')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all only root
     *
     * @return Category[]
     */
    public function findAllOnlyRoot()
    {
        return $this->em->createQueryBuilder()
            ->from(Category::class, 'c')
            ->select('c')
            ->orderBy('c.position', 'ASC')
            ->andWhere('c.parent IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find by
     *
     * @param ProductCategoryQuery $query
     *
     * @return Category[]
     */
    public function findBy(ProductCategoryQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Category::class, 'c')
            ->select('c');

        if ($query->hasIds()) {
            $qb
                ->andWhere('c.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        $qb->orderBy('c.position', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }
}
