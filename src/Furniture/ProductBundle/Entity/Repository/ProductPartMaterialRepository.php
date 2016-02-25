<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\ProductPartMaterial;

class ProductPartMaterialRepository extends EntityRepository
{
    /**
     * Has references to product?
     *
     * @param ProductPartMaterial $material
     *
     * @return bool
     */
    public function hasReferencedToProduct(ProductPartMaterial $material)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $result = $qb
            ->select('1')
            ->from('product_part_material', 'pm')
            ->innerJoin('pm', 'product_part_material_relation', 'pmr', 'pmr.product_part_material_id = pm.id')
            ->innerJoin('pmr', 'furniture_product_part', 'pr', 'pr.id = pmr.product_part_id')
            ->andWhere('pm.id = :variant')
            ->setParameter('variant', $material->getId())
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
