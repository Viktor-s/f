<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Product\Model\Attribute;

class AttributeRepository extends TranslatableResourceRepository
{
    /**
     * Has references to product?
     *
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function hasReferencedToProduct(Attribute $attribute)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $result = $qb
            ->select('1')
            ->from('sylius_product_attribute', 'a')
            ->innerJoin('a', 'sylius_product_attribute_value', 'v', 'a.id = v.attribute_id')
            ->innerJoin('v', 'product', 'p', 'p.id = v.product_id')
            ->andWhere('a.id = :attribute')
            ->setParameter('attribute', $attribute->getId())
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
