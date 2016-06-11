<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\Product;

class PdpIntellectualRootRepository
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
     * 
     * @param Product $product
     * @return \Furniture\ProductBundle\Entity\PdpIntellectualRoot
     */
    public function findRootForProduct(Product $product){
        
        return $this->em->createQueryBuilder()
            ->from(PdpIntellectualRoot::class, 'pir')
            ->select('pir')
            ->andWhere('pir.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
        
    }
    
}
