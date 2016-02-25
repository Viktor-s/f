<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;


class ProductPartMaterialVariantRepository extends EntityRepository
{
    /**
     * Has references to product?
     *
     * @param ProductPartMaterialVariant $variant
     *
     * @return bool
     */
    public function hasReferencedToProduct(ProductPartMaterialVariant $variant)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $result = $qb
            ->select('1')
            ->from('product_part_material_variant', 'pmv')
            ->innerJoin('pmv', 'product_part_material', 'pm', 'pm.id = pmv.product_extension_id')
            ->innerJoin('pm', 'product_part_material_relation', 'pmr', 'pmr.product_part_material_id = pm.id')
            ->innerJoin('pmr', 'furniture_product_part', 'pp', 'pp.id = pmr.product_part_id')
            ->innerJoin('pp', 'product', 'p', 'p.id = pp.product_id')
            ->andWhere('pmv.id = :variant')
            ->setParameter('variant', $variant->getId())
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
