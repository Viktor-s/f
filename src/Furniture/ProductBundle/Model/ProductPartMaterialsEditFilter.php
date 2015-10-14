<?php

namespace Furniture\ProductBundle\Model;

class ProductPartMaterialsEditFilter
{
    
    private $productPart;
    
    public function getProductPart()
    {
        return $this->productPart;
    }
    
    public function setProductPart($productPart)
    {
        $this->productPart = $productPart;
        return $this;
    }
    
    private $productPartMaterialVariants;
    
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

