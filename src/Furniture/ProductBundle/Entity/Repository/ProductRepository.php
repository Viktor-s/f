<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\ORM\NoResultException;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\Readiness;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepositiry;

class ProductRepository extends BaseProductRepositiry
{
    /**
     * Search none bundle by name
     *
     * @param string  $name
     * @param integer $limit
     * @param integer $offset
     *
     * @return \Furniture\ProductBundle\Entity\Product[]
     */
    public function searchNoneBundleByName($name, $limit = 5, $offset = 0)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->andWhere('translation.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->andWhere('SIZE(product.subProducts) = 0')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
    }

    /**
     * Search by name
     *
     * @param string  $name
     * @param integer $limit
     * @param integer $offset
     *
     * @return \Furniture\ProductBundle\Entity\Product[]
     */
    public function searchByName($name, $limit = 5, $offset = 0)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->andWhere('translation.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
    }

    /**
     * Has specification items for product
     *
     * @param Product $product
     *
     * @return bool
     */
    public function hasSpecificationItems(Product $product)
    {
        $variantIds = [];
        /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
        foreach ($product->getAllVariants() as $variant) {
            $variantIds[] = $variant->getId();
        }

        if (!count($variantIds)) {
            return false;
        }

        $qb = $this->getQueryBuilder();

        $query = $qb
            ->from(SpecificationItem::class, 'si')
            ->select('1')
            ->distinct()
            ->innerJoin('si.productVariant', 'pv')
            ->andWhere('pv.id IN (:identifiers)')
            ->setParameter('identifiers', $variantIds)
            ->getQuery();

        try {
            $result = $query->getSingleScalarResult();

            return (bool)$result;
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return \Pagerfanta\PagerfantaInterface
     */
    public function createFilterPaginator($criteria = [], $sorting = [], $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->addSelect('variant')
            ->leftJoin('product.variants', 'variant');

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('translation.name LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }

        if (!empty($criteria['factoryCode'])) {
            $queryBuilder
                ->andWhere('product.factoryCode LIKE :factory_code')
                ->setParameter('factory_code', '%' . $criteria['factoryCode'] . '%');
        }

        if (!empty($criteria['factory'])) {
            $queryBuilder
                ->andWhere('product.factory = :factory')
                ->setParameter('factory', $criteria['factory']);
        }

        if (!empty($criteria['priceFrom'])) {
            $queryBuilder
                ->andWhere('variant.price >= :price_from')
                ->setParameter('price_from', $criteria['priceFrom'] * 100);
        }

        if (!empty($criteria['priceTo'])) {
            $queryBuilder
                ->andWhere('variant.price <= :price_to')
                ->setParameter('price_to', $criteria['priceTo'] * 100);
        }

        if (!empty($criteria['statuses'])) {
            $queryBuilder
                ->innerJoin('product.readinesses', 'readiness');

            $andX = $queryBuilder->expr()->andX();

            $index = 0;
            array_map(function ($status) use ($queryBuilder, $andX, &$index) {
                $andX->add('readiness.id = :readiness_' . $index);
                $queryBuilder->setParameter('readiness_' . $index, $status);
                $index++;
            }, $criteria['statuses']);

            $queryBuilder->andWhere($andX);
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }

            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
            $queryBuilder->andWhere('product.deletedAt IS NOT NULL');
        }

        return $this->getPaginator($queryBuilder);
    }
}
