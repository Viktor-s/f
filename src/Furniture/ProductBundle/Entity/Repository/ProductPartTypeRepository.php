<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\ProductPartType;

class ProductPartTypeRepository extends EntityRepository
{
    /**
     * Has references to products?
     *
     * @param ProductPartType $type
     *
     * @return bool
     */
    public function hasReferencesToProducts(ProductPartType $type)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product', 'p')
            ->innerJoin('p', 'furniture_product_part', 'pp', 'p.id = pp.product_id')
            ->andWhere('pp.product_part_type_id = :type_id')
            ->setParameter('type_id', $type->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
