<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\ProductBundle\Entity\Product;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\Taxon;

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

        $pagination = new Pagerfanta(new DoctrineORMAdapter($qb->getQuery()));
        $pagination->setMaxPerPage($limit);
        $pagination->setCurrentPage($page);

        return $pagination;
    }
    
    /**
     * Find latest product by with limit
     * 
     * @param ProductQuery $query
     * @param type $limit
     */
    public function fundLatestBy(ProductQuery $query, $limit = 5)
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

        // Filtering by taxons
        if ($query->hasTaxons()) {
            $taxonIds = array_map(function (Taxon $taxon) {
                return $taxon->getId();
            }, $query->getTaxons());

            $qb
                ->innerJoin('p.taxons', 't')
                ->andWhere('t.id IN (:taxons)')
                ->setParameter('taxons', $taxonIds);
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

        if ($query->hasContentUser()) {
            $qb
                ->leftJoin(FactoryUserRelation::class, 'fur', 'WITH', 'fur.factory = f.id AND fur.user = :content_user AND fur.userAccept = :user_accept AND fur.factoryAccept = :factory_accept')
                ->setParameter('content_user', $query->getContentUser())
                ->setParameter('user_accept', true)
                ->setParameter('factory_accept', true);

            $orExpr = $qb->expr()->orX();
            $orExpr
                ->add('fur.accessProducts = :content_user_access_products')
                ->add('fdr.accessProducts = :default_access_products');

            $qb
                ->andWhere($orExpr)
                ->setParameter('content_user_access_products', true)
                ->setParameter('default_access_products', true);
        } else {
            $qb
                ->andWhere('fdr.accessProducts = :default_access_products')
                ->setParameter('default_access_products', true);
        }

        if ($query->isOnlyAvailable()) {
            $qb
                ->andWhere('p.availableOn <= :now')
                ->setParameter('now', new \DateTime());
        }

        return $qb;
    }
}
