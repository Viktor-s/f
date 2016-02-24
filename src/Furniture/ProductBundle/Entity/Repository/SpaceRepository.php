<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\Space;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class SpaceRepository extends TranslatableResourceRepository
{
    /**
     * Has products by space?
     *
     * @param Space $space
     *
     * @return bool
     */
    public function hasProducts(Space $space)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product_spaces', 'ps')
            ->andWhere('ps.space_id = :space_id')
            ->setParameter('space_id', $space->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
