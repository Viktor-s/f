<?php

namespace Furniture\SkuOptionBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SkuOptionVariant {
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var \Furniture\ProductBundle\Entity\Product
     */
    protected $product;
    
    /**
     *
     * @var \Furniture\SkuOptionBundle\Entity\SkuOptionType
     */
    protected $skuOptionType;
    
    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $value;
    
    function __construct() {}
    
    /**
     * @return string
     */
    public function getName(){
        return $this->getSkuOptionType()->getName();
    }

        /**
     * 
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function getProduct(){
        return $this->product;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionVaraint
     */
    public function setProduct(\Furniture\ProductBundle\Entity\Product $product){
        $this->product = $product;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionType
     */
    public function getSkuOptionType(){
        return $this->skuOptionType;
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionType $sku_option_type
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionVaraint
     */
    public function setSkuOptionType(\Furniture\SkuOptionBundle\Entity\SkuOptionType $sku_option_type){
        $this->skuOptionType = $sku_option_type;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * 
     * @param string $value
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionVariant
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }
}

