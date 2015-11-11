<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\CompositeCollectionQuery;

class CompositeCollectionRepository
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
     * Find all
     *
     * @return CompositeCollection[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(CompositeCollection::class, 'cc')
            ->select('cc')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find by
     *
     * @param CompositeCollectionQuery $query
     *
     * @return CompositeCollection[]
     */
    public function findBy(CompositeCollectionQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(CompositeCollection::class, 'cc')
            ->select('cc');

        if ($query->hasIds()) {
            $qb
                ->andWhere('cc.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        if($query->hasFactories()) {
            $factoryIds = array_map(function (Factory $factory) {
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->innerJoin('cc.factory', 'f')
                ->andWhere('f.id IN (:factory_ids)')
                ->setParameter('factory_ids', $factoryIds);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
}
