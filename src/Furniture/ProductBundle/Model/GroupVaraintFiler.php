<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductScheme;

class GroupVaraintFiler {

    /**
     *
     * @var \Furniture\ProductBundle\Entity\Product
     */
    private $product;

    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductScheme
     */
    private $scheme;
    
    /**
     *
     * @var ArrayCollection|\Sylius\Component\Product\Model\OptionValue[]
     */
    private $optionValues;

    /**
     *
     * @var type 
     */
    private $skuOptionsValues;

    /**
     * 
     */
    private $productPartMaterialVariants;
    private $productPartMaterial;

    private $skuPrice = null;
    
    /**
     * 
     * @param Product $product
     */
    function __construct(Product $product, ProductScheme $productScheme = null) {
        if($product->isSchematicProductType() && (!$productScheme || $productScheme->getProduct()->getId() != $product->getId() )){
            throw new \Exception('Schematic product must filtering with schema!');
        }
        $this->scheme = $productScheme;
        $this->product = $product;
        $this->optionValues = new ArrayCollection();
        $this->skuOptionsValues = new ArrayCollection();
        $this->productPartMaterialVariants = new ArrayCollection();
        if( $this->product->isSchematicProductType() ){
            foreach ($this->scheme->getProductParts() as $pp) {
                $ppf = new ProductPartMaterialsEditFilter();
                $ppf->setProductPart($pp);
                $this->productPartMaterialVariants->add($ppf);
            }
        }else{
            foreach ($this->product->getProductParts() as $pp) {
                $ppf = new ProductPartMaterialsEditFilter();
                $ppf->setProductPart($pp);
                $this->productPartMaterialVariants->add($ppf);
            }
        }
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * 
     * @return ArrayCollection
     */
    public function getOptionValues() {
        return $this->optionValues;
    }

    /**
     * 
     * @param ArrayCollection $optionValues
     * @return \Furniture\ProductBundle\Model\GroupVaraintEditFiler
     */
    public function setOptionValues(ArrayCollection $optionValues) {
        $this->optionValues = $optionValues;
        return $this;
    }

    public function getSkuOptionsValues() {
        return $this->skuOptionsValues;
    }

    public function setSkuOptionsValues($skuOptionsValues) {
        $this->skuOptionsValues = $skuOptionsValues;
        return $this;
    }

    public function getProductPartMaterialVariants() {
        return $this->productPartMaterialVariants;
    }

    public function setProductPartMaterialVariants($productPartMaterialVariants) {
        $this->productPartMaterialVariants = $productPartMaterialVariants;
        return $this;
    }
    
    public function setSkuPrice($skuPrice)
    {
        $this->skuPrice = $skuPrice;
        return $this;
    }
    
    public function getSkuPrice()
    {
        return $this->skuPrice;
    }

    public function getSkuPriceCent()
    {
        return (int)ceil($this->skuPrice*100);
    }
    
    public function getFilteredVariants() {
        $filtered = new ArrayCollection();

        foreach ($this->getProduct()->getVariants() as $variant) {
            
            /* if incorrect product scheme */
            if($this->isSchematicProductType() 
                    && $variant->getProductScheme()->getId() !== $this->getScheme()->getId() ){
                continue;
            }
            
            /* if incorrect price */
            if($this->getSkuPrice() !== null && $variant->getPrice() != $this->getSkuPriceCent())
            {
                continue;
            }
            
            /* if sku options exists */
            foreach ($variant->getSkuOptions() as $skuOption) {
                $callbackForExists = function ($k, $e) use ($skuOption) {
                    return $e->getId() == $skuOption->getId();
                };

                if (!$this->getSkuOptionsValues()->exists($callbackForExists)) {
                    continue 2;
                }
            }

            /* product variant selection exists */
            foreach ($variant->getProductPartVariantSelections() as $vs) {
                $callbackForExists = function($k, $e) use ($vs) {
                    return (
                            $e->getProductPart()->getId() == $vs->getProductPart()->getId() && $e->getProductPartMaterialVariants()->exists(function($k, $v) use ($vs) {
                                return $v->getId() == $vs->getProductPartMaterialVariant()->getId();
                            })
                            );
                };

                if (!$this->getProductPartMaterialVariants()->exists($callbackForExists)) {
                    continue 2;
                }
            }

            /* if option exists */
            foreach ($variant->getOptions() as $option) {
                if (!$this->getOptionValues()->contains($option)) {
                    continue 2;
                }
            }


            $filtered->add($variant);
        }
        return $filtered;
    }
    
    public function isSchematicProductType(){
        return $this->product->isSchematicProductType();
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function getScheme(){
        return $this->scheme;
    }

}
