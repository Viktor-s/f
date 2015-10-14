<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;
use Furniture\ProductBundle\Model\GroupVaraintEdit;

class ProductVariantRepository extends BaseProductVariantRepository
{
    
    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin($this->getAlias().'.skuOptions', 'skuOption')
            ->addSelect('skuOption')
            ->leftJoin($this->getAlias().'.productPartVariantSelections', 'productPartVariantSelection')
            ->addSelect('productPartVariantSelection')
        ;
    }
}
