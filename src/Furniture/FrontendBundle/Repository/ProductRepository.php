<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Type;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ProductRepository
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
     * Find product by identifier
     *
     * @param int $product
     *
     * @return Product
     */
    public function find($product)
    {
        return $this->em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p')
            ->andWhere('p.id = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by
     *
     * @param ProductQuery $query
     * @param int          $page
     * @param int          $limit
     *
     * @return Pagerfanta
     */
    public function findBy(ProductQuery $query, $page = 1, $limit = 12)
    {
        $qb = $this->createQueryBuilderForProductQuery($query);

        if ($page === null) {
            // Not use pagination
            return $qb
                ->getQuery()
                ->getResult();
        }

        $pagination = new Pagerfanta(new DoctrineORMAdapter($qb->getQuery(), false));
        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $pagination;
    }
    
    /**
     * Find latest product by with limit
     * 
     * @param ProductQuery $query
     * @param integer      $limit
     *
     * @return Product[]
     */
    public function findLatestBy(ProductQuery $query, $limit = 5)
    {
        $qb = $this->createQueryBuilderForProductQuery($query);
        $qb->orderBy('p.availableOn', 'desc');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Create query builder for product query
     *
     * @param ProductQuery $query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilderForProductQuery(ProductQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p')
            ->innerJoin('p.factory', 'f');

        // Filtering by space
        if ($query->hasSpaces()) {
            $spaceIds = array_map(function (Space $space) {
                return $space->getId();
            }, $query->getSpaces());

            $qb
                ->innerJoin('p.spaces', 'psp')
                ->andWhere('psp.id IN (:spaces)')
                ->setParameter('spaces', $spaceIds);
        }

        // Filtering by categories
        if ($query->hasCategories()) {
            $categoryIds = array_map(function (Category $category) {
                return $category->getId();
            }, $query->getCategories());

            $qb
                ->innerJoin('p.categories', 'pc')
                ->andWhere('pc.id IN (:categories)')
                ->setParameter('categories', $categoryIds);
        }

        // Filtering by types
        if ($query->hasTypes()) {
            $typeIds = array_map(function (Type $type) {
                return $type->getId();
            }, $query->getTypes());

            $qb
                ->innerJoin('p.types', 'pt')
                ->andWhere('pt.id IN (:types)')
                ->setParameter('types', $typeIds);
        }

        // Filtering by styles
        if ($query->hasStyles()) {
            $styleIds = array_map(function (Style $style) {
                return $style->getId();
            }, $query->getStyles());

            $qb
                ->innerJoin('p.styles', 'pst')
                ->andWhere('pst.id IN (:styles)')
                ->setParameter('styles', $styleIds);
        }

        if ($query->hasCompositeCollections()) {
            $compositeCollectionIds = array_map(function (CompositeCollection $compositeCollection) {
                return $compositeCollection->getId();
            }, $query->getCompositeCollections());

            $qb
                ->innerJoin('p.compositeCollections', 'cc')
                ->andWhere('cc.id IN (:composite_collections)')
                ->setParameter('composite_collections', $compositeCollectionIds);
        }

        // Filtered by factories
        if ($query->hasFactories()) {
            $factoryIds = array_map(function(Factory $factory){
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->andWhere('f.id IN (:factories)')
                ->setParameter('factories', $factoryIds);
        }

        // Check granted to view product
        $qb
            ->innerJoin('f.defaultRelation', 'fdr');

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
        } else {
            $qb
                ->andWhere('fdr.accessProducts = :default_access_products')
                ->setParameter('default_access_products', true);
        }

        if ($query->isOnlyAvailable()) {
            $qb
                ->andWhere('p.availableOn <= :now')
                ->andWhere('p.availableForSale = true')
                ->setParameter('now', new \DateTime());
        }

        return $qb;
    }
}
