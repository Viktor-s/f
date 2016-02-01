<?php

namespace Furniture\RetailerBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class RetailerProfileRepository extends EntityRepository
{
    /**
     * Create filter paginator
     *
     * @param array $criteria
     *
     * @return Pagerfanta
     */
    public function createFilterPaginator($criteria = [])
    {
        $qb = $this->createQueryBuilder('rp');

        if (!empty($criteria['name'])) {
            $qb
                ->andWhere('rp.name LIKE :name')
                ->setParameter('name', '%' . $criteria['name'] . '%');
        }

        $qb->orderBy('rp.id', 'DESC');

        return new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
    }
}
