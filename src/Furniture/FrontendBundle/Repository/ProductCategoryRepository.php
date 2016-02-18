<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
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

    /**
     * Find categories for factory
     *
     * @param $factory_id
     *
     * @return Category[]
     */
    public function findByFactory($factory_id)
    {
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata(Category::class, 'pc');

        $qb = new QueryBuilder($this->em->getConnection());
        $sql = $qb->select('pc.id, pc.parent_id, pc.slug, pc.position')
            ->from('product_category', 'pc')
            ->innerJoin('pc', 'product_categories', 'pcs', 'pcs.category_id = pc.id')
            ->innerJoin('pcs', 'product', 'p', 'p.id = pcs.product_id')
            ->innerJoin('pcs', 'factory', 'f', 'f.id = p.factory_id')
            ->where('f.id = :id')
            ->getSQL();
        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter('id', $factory_id);

        return $query->getResult();
    }
}
