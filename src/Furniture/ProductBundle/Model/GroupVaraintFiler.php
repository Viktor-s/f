<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;

class GroupVaraintFiler {

    /**
     *
     * @var \Furniture\ProductBundle\Entity\Product
     */
    private $product;

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

    /**
     * 
     * @param Product $product
     */
    function __construct(Product $product) {
        $this->product = $product;

        $this->optionValues = new ArrayCollection();
        $this->skuOptionsValues = new ArrayCollection();
        $this->productPartMaterialVariants = new ArrayCollection();
        foreach ($this->product->getProductParts() as $pp) {
            $ppf = new ProductPartMaterialsEditFilter();
            $ppf->setProductPart($pp);
            $this->productPartMaterialVariants->add($ppf);
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

    public function getFilteredVariants() {
        $filtered = new ArrayCollection();

        foreach ($this->getProduct()->getVariants() as $variant) {
            foreach ($variant->getSkuOptions() as $skuOption) {
                $callbackForExists = function ($k, $e) use ($skuOption) {
                    return $e->getId() == $skuOption->getId();
                };

                if (!$this->getSkuOptionsValues()->exists($callbackForExists)) {
                    continue 2;
                }
            }

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

            foreach ($variant->getOptions() as $option) {
                if (!$this->getOptionValues()->contains($option)) {
                    continue 2;
                }
            }


            $filtered->add($variant);
        }
        return $filtered;
    }

}
