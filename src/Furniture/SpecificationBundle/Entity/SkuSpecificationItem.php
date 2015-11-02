<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints as Assert;

class SkuSpecificationItem
{
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
     * @var \Furniture\SpecificationBundle\Entity\SpecificationItem
     */
    private $specificationItem;

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
     * Set product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return SkuSpecificationItem
     */
    public function setProductVariant(ProductVariant $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }

    /**
     * Get product variant
     *
     * @return ProductVariant
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * Set composition
     *
     * @param Composite $composite
     *
     * @return SkuSpecificationItem
     */
    public function setComposition(Composite $composite)
    {
        $this->composite = $composite;

        return $this;
    }

    /**
     * Get composite
     *
     * @return Composite
     */
    public function getComposite()
    {
        return $this->composite;
    }
    
    /**
     * Set specification item
     *
     * @param SpecificationItem $specificationItem
     *
     * @return SkuSpecificationItem
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
