<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\Product;
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
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('specification_item', 'si')
            ->innerJoin('si', 'sku_specification_item', 'ssi', 'ssi.speicifcation_item_id = si.id')
            ->innerJoin('ssi', 'product_variant', 'pv', 'pv.id = ssi.product_id')
            ->innerJoin('pv', 'product', 'p', 'pv.product_id = pv.id')
            ->andWhere('p.id = :product_id')
            ->setParameter('product_id', $product->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if ($result) {
            return true;
        } else {
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
        $queryBuilder = parent::getCollectionQueryBuilder();
        $queryBuilder->leftJoin('product.variants', 'variant');

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('LOWER(translation.name) LIKE :name')
                ->setParameter('name', '%' . mb_strtolower($criteria['name']) . '%');
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

        // Price filter.
        $andX = $queryBuilder->expr()->andX();

        if (!empty($criteria['priceFrom'])) {
            $exprPriceFrom = $queryBuilder->expr()->gte('variant.price', ':price_from');
            $queryBuilder->setParameter('price_from', $criteria['priceFrom'] * 100);
            $andX->add($exprPriceFrom);
        }

        if (!empty($criteria['priceTo'])) {
            $exprPriceTo = $queryBuilder->expr()->lte('variant.price', ':price_to');
            $queryBuilder->setParameter('price_to', $criteria['priceTo'] * 100);
            $andX->add($exprPriceTo);
        }

        $queryBuilder->andWhere($andX);

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
