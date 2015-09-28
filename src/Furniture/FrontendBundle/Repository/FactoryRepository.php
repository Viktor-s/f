<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;

class FactoryRepository extends BaseFrontendRepository
{
    /**
     * Find newest factories
     *
     * @param int $limit
     * @param int $offset
     *
     * @return \Furniture\FactoryBundle\Entity\Factory[]
     */
    public function findNewest($limit = 5, $offset = 0)
    {
        return $this
            ->createQueryBuilder('f')
            ->select('f')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }
}
