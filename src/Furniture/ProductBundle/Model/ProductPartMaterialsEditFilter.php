<?php

namespace Furniture\ProductBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class ProductPartMaterialsEditFilter
{
    
    private $productPart;
    
    private $productPartMaterialVariants;
    
    function __construct() {
        $this->productPartMaterialVariants = new ArrayCollection;
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function getProductPart()
    {
        return $this->productPart;
    }
    
    public function setProductPart($productPart)
    {
        $this->productPart = $productPart;
        return $this;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductPartMaterialVariants()
    {
        return $this->productPartMaterialVariants;
    }
    
    public function setProductPartMaterialVariants($productPartMaterialVariants)
    {
        $this->productPartMaterialVariants = $productPartMaterialVariants;
        return $this;
    }
    
}

