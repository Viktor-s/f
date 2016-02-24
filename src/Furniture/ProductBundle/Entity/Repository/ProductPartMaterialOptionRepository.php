<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\ProductPartMaterialOption;

class ProductPartMaterialOptionRepository extends EntityRepository
{
    /**
     * Has values?
     *
     * @param ProductPartMaterialOption $option
     *
     * @return bool
     */
    public function hasValues(ProductPartMaterialOption $option)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->from('product_part_material_option_value', 'ppmov')
            ->select('1')
            ->andWhere('ppmov.product_extension_option_id = :option_id')
            ->setParameter('option_id', $option->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
