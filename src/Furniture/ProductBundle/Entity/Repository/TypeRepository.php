<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\Type;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class TypeRepository extends TranslatableResourceRepository
{
    /**
     * Has products by type?
     *
     * @param Type $type
     *
     * @return bool
     */
    public function hasProducts(Type $type)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product_types', 'pt')
            ->andWhere('pt.type_id = :type_id')
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
