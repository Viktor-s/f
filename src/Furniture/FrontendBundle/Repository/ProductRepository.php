<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\Product;

class ProductRepository
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
     * Find product by identifier
     *
     * @param int $id
     *
     * @return Product|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
