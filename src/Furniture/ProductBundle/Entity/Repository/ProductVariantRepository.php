<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\ORM\NoResultException;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository as BaseProductVariantRepository;
use Furniture\ProductBundle\Model\GroupVaraintEdit;

class ProductVariantRepository extends BaseProductVariantRepository
{
    /**
     * Has specification item for variant?
     *
     * @param ProductVariant $variant
     *
     * @return bool
     */
    public function hasSpecificationItems(ProductVariant $variant)
    {
        $qb = $this->getQueryBuilder();

        $query = $qb
            ->from(SpecificationItem::class, 'si')
            ->select('1')
            ->distinct()
            ->innerJoin('si.productVariant', 'pv')
            ->andWhere('pv.id = :variant')
            ->setParameter('variant', $variant->getId())
            ->getQuery();

        try {
            $result = $query->getSingleScalarResult();

            return (bool)$result;
        } catch (NoResultException $e) {
            return false;
        }
    }

    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin($this->getAlias().'.skuOptions', 'skuOption')
            ->addSelect('skuOption')
            ->leftJoin($this->getAlias().'.productPartVariantSelections', 'productPartVariantSelection')
            ->addSelect('productPartVariantSelection');
    }
}
