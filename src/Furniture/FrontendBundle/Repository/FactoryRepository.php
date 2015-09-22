<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;

class FactoryRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
        return $this->em
            ->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }
}
