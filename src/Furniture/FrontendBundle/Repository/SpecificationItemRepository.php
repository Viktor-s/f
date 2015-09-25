<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\SpecificationItem;

class SpecificationItemRepository
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
     * Find specification item by id
     *
     * @param int $item
     *
     * @return SpecificationItem
     */
    public function find($item)
    {
        return $this->em->createQueryBuilder()
            ->from(SpecificationItem::class, 'si')
            ->select('si')
            ->andWhere('si.id = :item')
            ->setParameter('item', $item)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
