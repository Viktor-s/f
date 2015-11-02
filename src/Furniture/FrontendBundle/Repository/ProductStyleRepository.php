<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\Query\ProductStyleQuery;
use Furniture\ProductBundle\Entity\Style;

class ProductStyleRepository
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
     * @return Style[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Style::class, 's')
            ->select('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all with only root
     *
     * @return Style[]
     */
    public function findAllOnlyRoot()
    {
        return $this->em->createQueryBuilder()
            ->from(Style::class, 's')
            ->select('s')
            ->andWhere('s.parent IS NULL')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find by
     *
     * @param ProductStyleQuery $query
     *
     * @return Style[]
     */
    public function findBy(ProductStyleQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Style::class, 's')
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
