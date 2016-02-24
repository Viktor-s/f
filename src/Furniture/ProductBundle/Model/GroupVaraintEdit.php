<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Model\GroupVaraintFiler;
use Furniture\ProductBundle\Entity\Product;

class GroupVaraintEdit extends GroupVaraintFiler
{
    
    private $priceCalculator;
    
    private $width;
    
    private $height;
    
    private $depth;
    
    private $weight;
    
    private $factoryCodeUpdate;

    public function getPriceCalculator()
    {
        return $this->priceCalculator;
    }
    
    public function setPriceCalculator($priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
        return $this;
    }
    
    public function getWidth()
    {
        return $this->width;
    }
    
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }
    
    public function getHeight()
    {
        return $this->height;
    }
    
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }
    
    public function getDepth()
    {
        return $this->depth;
    }
    
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }
    
    public function getWeight()
    {
        return $this->weight;
    }
    
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }
    
     public function getFactoryCodeUpdate()
    {
        return $this->factoryCodeUpdate;
    }
    
    public function setFactoryCodeUpdate($factoryCodeUpdate)
    {
        $this->factoryCodeUpdate = $factoryCodeUpdate;
        return $this;
    }
    
}


