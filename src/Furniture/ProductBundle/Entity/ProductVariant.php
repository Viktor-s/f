<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;

use Doctrine\Common\Collections\ArrayCollection;

class ProductVariant extends BaseProductVariant {
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $skuOptions;
    
    function __construct() {
        parent::__construct();
        $this->skuOptions = new ArrayCollection();
    }
    
    /*
     * @return bool
     */
    public function hasSkuOptions(){
        return (bool)!$this->skuOptions->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSkuOptions(){
        return $this->skuOptions;
    }
    

    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Collection $sku_options
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function setSkuOptions(Collection $sku_options){
        $this->skuOptions = $sku_options;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return bool
     */
    public function hasSkuOption(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        return $this->skuOptions->contains($sku_option_variant);
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function addSkuOption(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        if(!$this->hasSkuOption($sku_option_variant)){
            $this->skuOptions[] = $sku_option_variant;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function removeSkuOption(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        if($this->hasSkuOption($sku_option_variant)){
            $this->skuOptions->removeElement($sku_option_variant);
        }
        return $this;
    }
    
}