<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPart extends AbstractTranslatable
{
    
    /**
     *
     * @var int
     */
    protected $id;
    
    /**
     *
     * @var ProductPartType
     */
    protected $prtoductPartType;
    
    /**
     *
     * @var \Furniture\ProductBundle\Entity\Product
     */
    protected $product;

    /**
     *
     * @var Collection
     */
    protected $productPartMaterials;
    
    function __construct() {
        parent::__construct();
        $this->productPartMaterials = new ArrayCollection();
    }


    /**
     * 
     * @return int
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->translate()->getLabel();
    }
    
    /**
     * 
     * @param string $label
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function setLabel($label)
    {
        $this->translate()->setLabel($label);
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartType $prtoductPartType
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function setPrtoductPartType(ProductPartType $prtoductPartType)
    {
        $this->prtoductPartType = $prtoductPartType;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPartType
     */
    public function getPrtoductPartType()
    {
        return $this->prtoductPartType;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Collection $productPartMaterials
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function setProductPartMaterials(Collection $productPartMaterials)
    {
        $this->productPartMaterials = $productPartMaterials;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\Collection
     */
    public function getProductPartMaterials()
    {
        return $this->productPartMaterials;
    }

    public function __toString() 
    {
        $this->getLabel();
    }
    
}

