<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;

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
     * Find by
     *
     * @param FactoryQuery $query
     *
     * @return Factory[]
     */
    public function findBy(FactoryQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f');

        if ($query->hasIds()) {
            $qb
                ->andWhere('f.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Find newest factories
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Factory[]
     */
    public function findNewest($limit = 5, $offset = 0)
    {
        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }

    /**
     * Find all
     *
     * @return Factory[]
     */
    public function findAll()
    {
        return $this->findBy(new FactoryQuery());
    }
}
