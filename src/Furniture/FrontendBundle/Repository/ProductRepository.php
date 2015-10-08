<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
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
        $qb = $this->em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p');

        if ($query->hasTaxons()) {
            $taxonIds = array_map(function (Taxon $taxon) {
                return $taxon->getId();
            }, $query->getTaxons());

            $qb
                ->innerJoin('p.taxons', 't')
                ->andWhere('t.id IN (:taxons)')
                ->setParameter('taxons', $taxonIds);
        }

        if ($query->hasFactories()) {
            $factoryIds = array_map(function(Factory $factory){
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->innerJoin('p.factory', 'f')
                ->andWhere('f.id IN (:factories)')
                ->setParameter('factories', $factoryIds);
        }

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
}
