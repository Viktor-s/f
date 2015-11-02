<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\Style;

class FactoryRepository
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
     * Find factory by identifier
     *
     * @param int $factory
     *
     * @return Factory|null
     */
    public function find($factory)
    {
        return $this->em->find(Factory::class, $factory);
    }

    /**
     * Find by
     *
     * @param FactoryQuery $query
     *
     * @return Factory[]
     */
    public function findBy(FactoryQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->distinct()
            ->select('f');

        if ($query->hasStyles() || $query->hasCategories()) {
            $qb
                ->innerJoin(Product::class, 'p', 'WITH', 'p.factory = f.id');
        }

        if ($query->hasStyles()) {
            $styleIds = array_map(function (Style $style) {
                return $style->getId();
            }, $query->getStyles());

            $qb
                ->innerJoin('p.styles', 'pst')
                ->andWhere('pst.id IN (:styles)')
                ->setParameter('styles', $styleIds);
        }

        if ($query->hasCategories()) {
            $categoryIds = array_map(function (Category $category) {
                return $category->getId();
            }, $query->getCategories());

            $qb
                ->innerJoin('p.categories', 'pc')
                ->andWhere('pc.id IN (:categories)')
                ->setParameter('categories', $categoryIds);
        }

        if ($query->hasIds()) {
            $qb
                ->andWhere('f.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Find newest factories
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Factory[]
     */
    public function findNewest($limit = 5, $offset = 0)
    {
        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }

    /**
     * Find all
     *
     * @return Factory[]
     */
    public function findAll()
    {
        return $this->findBy(new FactoryQuery());
    }
}
