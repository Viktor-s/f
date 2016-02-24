<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\Style;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class StyleRepository extends TranslatableResourceRepository
{
    /**
     * Has products by style?
     *
     * @param Style $style
     *
     * @return bool
     */
    public function hasProducts(Style $style)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product_styles', 'ps')
            ->andWhere('ps.style_id = :style_id')
            ->setParameter('style_id', $style->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
