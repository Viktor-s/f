<?php

namespace Furniture\SkuOptionBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use \Doctrine\ORM\Mapping\ClassMetadata;

class SkuOptionTypeRepository extends TranslatableResourceRepository
{
    /**
     * Has references to product?
     *
     * @param SkuOptionType $skuOptionType
     *
     * @return bool
     */
    public function hasReferencedToProduct(SkuOptionType $skuOptionType)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $result = $qb
            ->select('1')
            ->from('furniture_product_option_type', 'pot')
            ->innerJoin('pot', 'furniture_product_sku_option_variants', 'psov', 'psov.sku_option_id = pot.id')
            ->innerJoin('psov', 'product', 'p', 'p.id = psov.product_id')
            ->andWhere('pot.id = :skuOption')
            ->setParameter('skuOption', $skuOptionType->getId())
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
