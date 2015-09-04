<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Core\Model\Product as BaseProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Product extends BaseProduct {
     
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $subProducts;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $bundleProducts;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $skuOptionVariants;
    
    /**
     *
     * @var string
     */
    protected $factoryCode;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->variants = new ArrayCollection();
        $this->setMasterVariant(new ProductVariant());
        
        $this->subProducts = new ArrayCollection();
        $this->bundleProducts = new ArrayCollection();
        $this->skuOptionVariants = new ArrayCollection();
    }
    
    /*
     * @return bool
     */
    public function hasSubProducts(){
        return (bool)!$this->subProducts->isEmpty();
    }
    
    /*
     * @return bool
     */
    public function isBundle(){
        if($this->hasSubProducts()){
            return true;
        }
        if($this->bundleProducts->count()){
            return false;
        }
        return false;
    }
    
    /*
     * @return bool
     */
    public function isBelongable(){
        if( !$this->bundleProducts->isEmpty() ){
            return true;
        }
        if( !$this->hasSubProducts() ){
            return true;
        }
        return true;
    }
    
    /*
     * @return bool
     */
    public function isSimple(){
        if( $this->bundleProducts->isEmpty() && $this->subProducts->isEmpty() ){
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubProducts(){
        return $this->subProducts;
    }
    
    
    /**
     * 
     * @param Collection $bundle_products
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setSubProducts(Collection $bundle_products){
        $this->subProducts = $bundle_products;
        return $this;
    }
    
    /*
     * @param Product $product
     * @return bool
     */
    public function hasSubProduct(Product $product){
        return $this->subProducts->contains($product);
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return \Furniture\ProductBundle\Entity\Product
     * @throws \Exception
     */
    public function addSubProduct(Product $product){
        if(!$product->isBelongable()){
            throw new \Exception(__CLASS__.'::'.__METHOD__.' '.get_class($product).' must be belongable!');
        }
        if( $this->getId() && $this->getId() == $product->getId() ){
            throw new \Exception(__CLASS__.'::'.__METHOD__.' '.get_class($product).' cant contains it self!');
        }
        if(!$this->hasSubProduct($product)){
            $this->subProducts[] = $product;
        }
        
        return $this;
    }
    
    public function removeSubProduct(Product $product){
        if($this->hasSubProduct($product)){
            $this->subProducts->removeElement($product);
        }
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getFactoryCode(){
        return $this->factoryCode;
    }
    
    /**
     * 
     * @param string $factory_code
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setFactoryCode($factory_code){
        $this->factoryCode = $factory_code;
        return $this;
    }
    
    /*
     * @return bool
     */
    public function hasSkuOptionVariants(){
        return (bool)!$this->skuOptionVariants->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSkuOptionVariants(){
        return $this->skuOptionVariants;
    }
    
    public function getSkuOptionVariantsGrouped(){
        $grouped = [];
        
        $this->skuOptionVariants->forAll(function ($id, $sku_variant) use (&$grouped) {
            $opt_id = $sku_variant->getSkuOptionType()->getId();
            if( !isset($grouped[$opt_id]) ) 
                $grouped[$opt_id] = [];
            $grouped[$opt_id][] = $sku_variant;
            return true;
        });
        
        return $grouped;
    }
    
    /**
     * 
     * @param Collection $bundle_products
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setSkuOptionVariants(Collection $sku_option_variants){
        $this->skuOptionVariants = $sku_option_variants;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return type
     */
    public function hasSkuOptionVariant(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        return $this->skuOptionVariants->contains($sku_option_variant);
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function addSkuOptionVariant(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        if(!$this->hasSkuOptionVariant($sku_option_variant)){
            $sku_option_variant->setProduct($this);
            $this->skuOptionVariants[] = $sku_option_variant;
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function removeSkuOptionVariant(\Furniture\SkuOptionBundle\Entity\SkuOptionVariant $sku_option_variant){
        if($this->hasSkuOptionVariant($sku_option_variant)){
            $this->skuOptionVariants->removeElement($sku_option_variant);
        }
        return $this;
    }
    
    /**
     * Return translation model class.
     *
     * @return string
     */
    public static function getTranslationClass()
    {
        return get_parent_class(__CLASS__).'Translation';
    }
    
}
