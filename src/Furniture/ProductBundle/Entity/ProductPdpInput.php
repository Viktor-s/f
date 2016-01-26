<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Sylius\Component\Product\Model\Option;
use Doctrine\Common\Collections\Criteria;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantGrouped;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPdpInput
{
    /**
     * input type select
     */
    const SELECT_DEFAULT_TYPE = 0;

    /**
     * input select inline form
     */
    const SELECT_INLINE_TYPE = 1;
    
    /**
     * input select popup form
     */
    const SELECT_POPUP_TYPE = 2;
    
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductPdpConfig
     */
    private $config;

    /**
     * @var Option
     */
    private $option;

    /**
     * @var SkuOptionType
     */
    private $skuOption;

    /**
     * @var ProductPart
     */
    private $productPart;

    /**
     *
     * @var int
     */
    private $forSchemes;
    
    /**
     * @var int
     */
    private $position = 0;

    /**
     *
     * @var int
     */
    private $type;
    
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
     * Set config
     *
     * @param ProductPdpConfig $config
     *
     * @return ProductPdpInput
     */
    public function setConfig(ProductPdpConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return ProductPdpConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set option
     *
     * @param Option $option
     *
     * @return ProductPdpInput
     */
    public function setOption(Option $option)
    {
        $this->clearMappingFields();
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return Option|null
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * 
     * Get option values which exists on product variants
     * 
     * @return array
     */
    public function getOptionValues() {
        $criteria = new Criteria();
        $criteria->andWhere($criteria->expr()->eq('option', $this->getOption()));
        
        $values = [];
        foreach ($this->getConfig()->getProduct()->getVariants() as $variant) {
            /* @var $variant \Furniture\ProductBundle\Entity\ProductVariant */
            $value = $variant->getOptions()->matching($criteria)->first();
            $values[$value->getId()] = $value;
        }
        return $values;
    }
    
    /**
     * Set product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductPdpInput
     */
    public function setProductPart(ProductPart $productPart)
    {
        $this->productPart = $productPart;

        return $this;
    }

    /**
     * Get product part
     *
     * @return ProductPart|null
     */
    public function getProductPart()
    {
        return $this->productPart;
    }

    /**
     * Get array of \Furniture\ProductBundle\Entity\ProductPartVariantSelection
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPartVariantSelection[]
     */
    public function getProductPartMaterialsVariantSelections()
    {
        $criteria = new Criteria();
        $criteria->andWhere($criteria->expr()->eq('productPart', $this->getProductPart()));
        
        $variantSelections = [];
        foreach ($this->getConfig()->getProduct()->getVariants() as $variant) {
            /* @var $variant \Furniture\ProductBundle\Entity\ProductVariant */
            if($variantSelection = $variant->getProductPartVariantSelections()->matching($criteria)->first()){
                $variantSelections[$variantSelection->getProductPartMaterialVariant()->getId()] = $variantSelection;
            }
        }
        return $variantSelections;
    }

    public function getProductPartMaterialsVariantGrouped(){
        
        $grouped = new ArrayCollection();
        
        foreach($this->getProductPartMaterialsVariantSelections() as $variantSelection){
            $productPartMaterialVariant = $variantSelection->getProductPartMaterialVariant();
            $productPartMaterial = $productPartMaterialVariant->getProductPartMaterial();
            #создать новый обьект для групировки!
            if(!$grouped->containsKey($productPartMaterial->getId())){
                $grouped[$productPartMaterial->getId()] = new ProductPartMaterialVariantGrouped($productPartMaterial);
            }
            $grouped[$productPartMaterial->getId()]->add($productPartMaterialVariant);
        }
        return $grouped;
    }

    /**
     * Set sku option
     *
     * @param SkuOptionType $skuOption
     *
     * @return ProductPdpInput
     */
    public function setSkuOption(SkuOptionType $skuOption)
    {
        $this->clearMappingFields();
        $this->skuOption = $skuOption;

        return $this;
    }

    /**
     * Get sku option
     *
     * @return SkuOptionType|null
     */
    public function getSkuOption()
    {
        return $this->skuOption;
    }

    /**
     * 
     * Get sku option variants which exists on product variants
     * 
     * @return array
     */
    public function getSkuOptionVariants() {
        $criteria = new Criteria();
        $criteria->andWhere($criteria->expr()->eq('skuOptionType', $this->getSkuOption()));
        
        $variants = [];
        foreach ($this->getConfig()->getProduct()->getVariants() as $variant) {
            /* @var $variant \Furniture\ProductBundle\Entity\ProductVariant */
            if($variant = $variant->getSkuOptions()->matching($criteria)->first()){
                $variants[$variant->getId()] = $variant;
            }
        }
        return $variants;
    }
    
    public function setForSchemes($status){
        $this->forSchemes = $status;
        return $this;
    }
    
    public function isForSchemes(){
        return $this->forSchemes;
    }

    public function getSchemes(){
        if($this->isForSchemes()){
            return $this->getConfig()->getProduct()->getProductSchemes();
        }
        return null;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return ProductPdpInput
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * 
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * 
     * @param int $type
     * @return \Furniture\ProductBundle\Entity\ProductPdpInput
     */
    public function  setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function getHumanNameDetailed(){
        if ($this->productPart) {
            /** @var ProductPartTranslation $translation */
            $translation = $this->productPart->translate();

            return $translation->getLabel().' ('.$this->productPart->getProductPartType()->getCode().')';
        } else if ($this->skuOption) {
            return sprintf(
                '%s',
                $this->skuOption->getName()
            );
        } else if ($this->option) {
            return sprintf(
                '%s',
                $this->option->getName()
            );
        } else if ($this->isForSchemes()) {
            return 'Variants';
        } else {
            return 'Undefined';
        }
    }
    
    /**
     * Get human name
     *
     * @return string
     */
    public function getHumanName()
    {
        if ($this->productPart) {
            /** @var ProductPartTranslation $translation */
            $translation = $this->productPart->translate();

            return sprintf(
                '%s',
                $translation->getLabel()
            );
        } else if ($this->skuOption) {
            return sprintf(
                '%s',
                $this->skuOption->getName()
            );
        } else if ($this->option) {
            return sprintf(
                '%s',
                $this->option->getName()
            );
        } else if ($this->isForSchemes()) {
            return 'Variants';
        } else {
            return 'Undefined';
        }
    }

    /**
     * Clear mapping fields
     */
    private function clearMappingFields()
    {
        $this->option = null;
        $this->skuOption = null;
        $this->productPart = null;
    }
}
