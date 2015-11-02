<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\Query\ProductSpaceQuery;
use Furniture\ProductBundle\Entity\Space;

class ProductSpaceRepository
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
     * @return Space[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Space::class, 's')
            ->select('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all with only root
     *
     * @return Space[]
     */
    public function findAllOnlyRoot()
    {
        return $this->em->createQueryBuilder()
            ->from(Space::class, 's')
            ->select('s')
            ->andWhere('s.parent IS NULL')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find by
     *
     * @param ProductSpaceQuery $query
     *
     * @return Space[]
     */
    public function findBy(ProductSpaceQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Space::class, 's')
            ->select('s');

        if ($query->hasIds()) {
            $qb
                ->andWhere('s.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        $qb->orderBy('s.position', 'ASC');

        return $qb
            ->getQuery()
            ->getResult();
    }
}
