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
            $words= explode(' ', $criteria['name']);
            $words = array_map('trim', $words);

            $orX = $qb->expr()->orX();

            foreach ($words as $index => $word) {
                $key = sprintf(':name_word_%d', $index);
                $orX->add('LOWER(rp.name) LIKE ' . $key);
                $qb->setParameter($key, '%' . mb_strtolower($word) . '%');
            }

            $qb
                ->andWhere($orX);
        }

        $qb->orderBy('rp.id', 'DESC');

        return new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
    }
}
