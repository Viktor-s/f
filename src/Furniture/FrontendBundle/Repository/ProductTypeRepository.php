<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\Query\ProductTypeQuery;
use Furniture\ProductBundle\Entity\Type;

class ProductTypeRepository
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
     * @return Type[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Type::class, 's')
            ->select('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all with only root
     *
     * @return Type[]
     */
    public function findAllOnlyRoot()
    {
        return $this->em->createQueryBuilder()
            ->from(Type::class, 's')
            ->select('s')
            ->andWhere('s.parent IS NULL')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find by
     *
     * @param ProductTypeQuery $query
     *
     * @return Type[]
     */
    public function findBy(ProductTypeQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Type::class, 's')
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
