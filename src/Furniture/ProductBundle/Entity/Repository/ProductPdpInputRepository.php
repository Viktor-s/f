<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\ProductPart;

class ProductPdpInputRepository extends EntityRepository
{
    /**
     * Find product pdp input by product part
     *
     * @param ProductPart $productPart
     *
     * @return \Furniture\ProductBundle\Entity\ProductPdpInput
     */
    public function findByProductPart(ProductPart $productPart)
    {
        return $this->createQueryBuilder('ppi')
            ->andWhere('ppi.productPart = :product_part')
            ->setParameter('product_part', $productPart)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
