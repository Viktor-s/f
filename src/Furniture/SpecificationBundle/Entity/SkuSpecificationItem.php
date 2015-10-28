<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints as Assert;

class SkuSpecificationItem {
    
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var \Furniture\ProductBundle\Entity\ProductVariant
     *
     * @Assert\NotBlank()
     */
    private $productVariant;
    
    /**
     * @var \Furniture\CompositionBundle\Entity\Composite
     */
    private $composite;
    
    /**
     *
     * @var \Furniture\SpecificationBundle\Entity\SpecificationItem
     */
    private $specificationItem;
    
    public function __construct() {
        ;
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set ProductVariant
     * @param \Furniture\ProductBundle\Entity\ProductVariant $productVariant
     * @return \Furniture\SpecificationBundle\Entity\SkuSpecificationItem
     */
    public function setProductVariant(ProductVariant $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }

    /**
     * Get ProductVariant
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * 
     * @param \Furniture\CompositionBundle\Entity\Composite $composite
     * @return \Furniture\SpecificationBundle\Entity\SkuSpecificationItem
     */
    public function setComposition(Composite $composite)
    {
        $this->composite = $composite;

        return $this;
    }

    /**
     * 
     * @return \Furniture\CompositionBundle\Entity\Composite
     */
    public function getComposite()
    {
        return $this->composite;
    }
    
    /**
     * 
     * @param \Furniture\SpecificationBundle\Entity\SpecificationItem $specificationItem
     * @return \Furniture\SpecificationBundle\Entity\SkuSpecificationItem
     */
    public function setSpecificationItem(SpecificationItem $specificationItem)
    {
        $this->specificationItem = $specificationItem;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\SpecificationBundle\Entity\SpecificationItem
     */
    public function getSpecificationItem()
    {
        return $this->specificationItem;
    }
    
}
