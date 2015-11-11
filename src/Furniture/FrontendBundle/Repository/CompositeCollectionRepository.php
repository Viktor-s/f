<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Entity\CompositeCollection;
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
            /* Доделаем после того как переделаем связи! */
        }
        return $qb
            ->getQuery()
            ->getResult();
    }
}
