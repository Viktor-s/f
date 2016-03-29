<?php

namespace Furniture\FactoryBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class FactoryRepository extends TranslatableResourceRepository
{
    /**
     * Has products by factory
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasProducts(Factory $factory)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product', 'p')
            ->andWhere('p.factory_id = :factory_id')
            ->setParameter('factory_id', $factory->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }

    /**
     * Has customers by factory
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasCustomers(Factory $factory)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('users', 'u')
            ->andWhere('u.factory_id = :factory_id')
            ->setParameter('factory_id', $factory->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }

    /**
     * Has product part materials by factory?
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasProductPartMaterials(Factory $factory)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product_part_material', 'ppm')
            ->andWhere('ppm.factory_id = :factory_id')
            ->setParameter('factory_id', $factory->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
