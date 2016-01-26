<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
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
        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->andWhere('f.enabled = true')
            ->andWhere('f.id = :factory')
            ->setParameter('factory', $factory)
            ->getQuery()
            ->getOneOrNullResult();
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
        $qb = $this->createQueryBuilderForFactory($query);

        $query = $qb->getQuery();

        return $query
            ->getResult();
    }

    /**
     * Find newest factories
     *
     * @param FactoryQuery $query
     * @param int          $limit
     * @param int          $offset
     *
     * @return Factory[]
     */
    public function findNewest(FactoryQuery $query, $limit = 5, $offset = 0)
    {
        return $this->createQueryBuilderForFactory($query)
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

    /**
     * Create query builder for factory query
     *
     * @param FactoryQuery $query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForFactory(FactoryQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->distinct()
            ->select('f')
            // If visible in front!
            ->andWhere('f.enabled = true');

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

        // Check granted to access
        $qb->innerJoin('f.defaultRelation', 'fdr');

        if ($query->hasRetailer()) {
            $qb
                ->leftJoin(FactoryRetailerRelation::class, 'frr', 'WITH', 'frr.factory = f.id AND frr.retailer = :retailer AND frr.retailerAccept = :retailer_accept AND frr.factoryAccept = :factory_accept')
                ->setParameter('retailer', $query->getRetailer())
                ->setParameter('retailer_accept', true)
                ->setParameter('factory_accept', true);

            $orExpr = $qb->expr()->orX();
            $orExpr
                ->add('frr.accessProducts = :retailer_access_products')
                ->add('fdr.accessProducts = :default_access_products');

            $qb
                ->andWhere($orExpr)
                ->setParameter('retailer_access_products', true)
                ->setParameter('default_access_products', true);

            if ($query->getRetailer()->isDemo()) {
                // Should filtered by demo
                $demoFactoryIds = array_map(function (Factory $factory) {
                    return $factory->getId();
                }, $query->getRetailer()->getDemoFactories()->toArray());

                $qb
                    ->andWhere('f.id IN (:demo_factories)')
                    ->setParameter('demo_factories', $demoFactoryIds);
            }
        } else {
            $qb
                ->andWhere('fdr.accessProducts = :default_access_products')
                ->setParameter('default_access_products', true);
        }

        return $qb;
    }
}
