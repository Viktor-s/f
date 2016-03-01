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
            ->from('product_part_material_variant', 'ppmv')
            ->innerJoin('ppmv', 'product_part_material', 'ppm', 'ppm.id = ppmv.product_extension_id')
            ->innerJoin('ppm', 'furniture_product_part_variant_selection', 'fppvs', 'fppvs.product_part_material_variant_id = ppmv.id')
            ->innerJoin('fppvs', 'furniture_product_part', 'fpp', 'fpp.id = fppvs.product_part_id')
            ->innerJoin('fpp', 'product', 'p', 'p.id = fpp.product_id')
            ->andWhere('ppmv.id = :variant')
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
