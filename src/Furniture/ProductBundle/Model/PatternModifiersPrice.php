<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Entity\ProductVariantsPatternModifier;
use Furniture\ProductBundle\Pattern\ProductVariantParameters;

class PatternModifiersPrice {
    
    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductVariantsPattern
     */
    private $pattern;
    
    /**
     *
     * @var array[]|\Furniture\ProductBundle\Entity\ProductVariantsPatternModifier
     */
    private $modifiers = [];
    
    private $modifiersStructure = [];
    
    private $patternStructure = [];
    
    /**
     *
     * @var int
     */
    private $price;
    
    function __construct(ProductVariantsPattern $pattern, $modifiers) {
        $this->patternStructure = [
          'productParts' => [],
          'productSkuOptions' => [],
        ];
        
        /**
         * Pattern product part selected variants
         */
        foreach($pattern->getPartPatternVariantSelections() as $ppmvs){
            if( !isset($this->patternStructure['productParts'][$ppmvs->getProductPart()->getId()]) ){
                $this->patternStructure['productParts'][$ppmvs->getProductPart()->getId()] = [];
            }
            $this->patternStructure['productParts'][$ppmvs->getProductPart()->getId()][] = $ppmvs->getProductPartMaterialVariant()->getId();
        }
        
        /**
         * PAttern sku option selected
         */
        foreach($pattern->getSkuOptionValues() as $skuOptionValue){
            if( !isset($this->patternStructure['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()]) ){
                $this->patternStructure['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()] = [];
            }
            $this->patternStructure['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()][] = $skuOptionValue->getId();
        }
        
        foreach($modifiers as $modifier){
            $mod = [
                'modifier' => $modifier,
                'productParts' => [],
                'productSkuOptions' => [],
            ];
            /* @var $modifier \Furniture\ProductBundle\Entity\ProductVariantsPatternModifier */
            foreach( $modifier->getPartPatternVariantSelections() as $ppmvs ){
                if( !isset($mod['productParts'][$ppmvs->getProductPart()->getId()]) ){
                    $mod['productParts'][$ppmvs->getProductPart()->getId()] = [];
                }
                $mod['productParts'][$ppmvs->getProductPart()->getId()][] = $ppmvs->getProductPartMaterialVariant()->getId();
            }
            
            foreach($modifier->getSkuOptionValues() as $skuOptionValue){
                if( !isset($mod['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()]) ){
                    $mod['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()] = [];
                }
                $mod['productSkuOptions'][$skuOptionValue->getSkuOptionType()->getId()][] = $skuOptionValue->getId();
            }
            
            $this->modifiersStructure[$mod['modifier']->getId()] = $mod;
        }
        
        $validModifiers = [];
        //Compare if valid
        foreach($this->modifiersStructure as $modifier){
            $valid = false;
            //Compare product parts
            foreach($modifier['productParts'] as $productPartId => $productPartsVariants){
                if( !isset($this->patternStructure['productParts'][$productPartId]) ){
                    $valid = false;
                    break;
                }
                if( !empty(array_intersect( $productPartsVariants, $this->patternStructure['productParts'][$productPartId])) ){
                    $valid = true;
                }
            }
            
            //Compare sku options
             foreach($modifier['productSkuOptions'] as $skuOptionTypeId => $skuOptionValues){
                if( !isset($this->patternStructure['productSkuOptions'][$skuOptionTypeId]) ){
                    $valid = false;
                    break;
                }
                if( !empty(array_intersect( $skuOptionValues, $this->patternStructure['productSkuOptions'][$skuOptionTypeId])) ){
                    $valid = true;
                }
             }
            
            if($valid){
                $this->modifiers[] = $modifier['modifier'];
            }
        }
    }
    
    public function getCombinedModifiersWith(ProductVariantsPatternModifier $modifier){
        $combined = [];
        
        $modifierStructure = $this->modifiersStructure[$modifier->getId()];
        
        //Compare if valid
        foreach($this->modifiersStructure as $modifier){
            $valid = false;
            //Compare product parts
            foreach($modifier['productParts'] as $productPartId => $productPartsVariants){
                if( !isset($modifierStructure['productParts'][$productPartId]) ){
                    $valid = false;
                    break;
                }
                if( !empty(array_intersect( $productPartsVariants, $modifierStructure['productParts'][$productPartId])) ){
                    $valid = true;
                }
            }
            
            //Compare sku options
             foreach($modifier['productSkuOptions'] as $skuOptionTypeId => $skuOptionValues){
                if( !isset($modifierStructure['productSkuOptions'][$skuOptionTypeId]) ){
                    $valid = false;
                    break;
                }
                if( !empty(array_intersect( $skuOptionValues, $modifierStructure['productSkuOptions'][$skuOptionTypeId])) ){
                    $valid = true;
                }
             }
            
            if($valid){
                $combined[] = $modifier['modifier'];
            }
            return $combined;
        }
        
    }


    public function getModifiers(){
        return $this->modifiers;
    }
    
    public function getPriceIfSelected(ProductVariantParameters $parameters){
        
    }
    
}

