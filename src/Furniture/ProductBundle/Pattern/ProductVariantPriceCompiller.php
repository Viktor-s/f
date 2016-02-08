<?php
namespace Furniture\ProductBundle\Pattern;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductVariant;

class ProductVariantPriceCompiller {
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * Construct
     *
     * @param ProductVariantFinder   $finder
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * if $parameters null - only global modifier will be apply
     * 
     * @param \Furniture\ProductBundle\Pattern\ProductVariantsPattern $pattern
     * @param \Furniture\ProductBundle\Pattern\ProductVariantParameters $parameters
     * @return int
     */
    public function getPriceFor(ProductVariantsPattern $pattern, ProductVariantParameters $parameters = null){
        return;
    }
    
}

