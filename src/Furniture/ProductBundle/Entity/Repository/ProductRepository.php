<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\ORM\NoResultException;
use Furniture\ProductBundle\Entity\Product;
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
            ->setParameter('name', $name.'%')
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
                ->setParameter('name', $name.'%')
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

            return (bool) $result;
        } catch (NoResultException $e) {
            return false;
        }
    }
}
