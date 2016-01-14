<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPartMaterialVariantGrouped
{

    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductPartMaterial
     */
    private $productPartMaterial;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $productPartMaterialVariants;
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterial $productPartMaterial
     * @param \Doctrine\Common\Collections\Collection $productPartMaterialVariants
     */
    public function __construct(ProductPartMaterial $productPartMaterial, Collection $productPartMaterialVariants = null) {
        $this->productPartMaterial = $productPartMaterial;
        if( $productPartMaterialVariants ){
            
            foreach ( $productPartMaterialVariants as $variant ) $this->validateItem ($variant);
            
            $this->productPartMaterialVariants = $productPartMaterialVariants;
        }else{
            $this->productPartMaterialVariants = new ArrayCollection();
        }
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialVariant $productPartMaterialVariant
     * @return boolean
     * @throws \Exception
     */
    private function validateItem(ProductPartMaterialVariant $productPartMaterialVariant)
    {
        if ( $this->getProductPartMaterial()->getId() === $productPartMaterialVariant->getProductPartMaterial()->getId() ) {
            return true;
        }else{
            throw new \Exception(__CLASS__.'::'.__METHOD__.' ProductPartMaterialVariant object has invalid ProductPartMaterial');
        }
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialVariant $productPartMaterialVariant
     * @return \Furniture\ProductBundle\Model\ProductPartMaterialVariantGrouped
     */
    public function add(ProductPartMaterialVariant $productPartMaterialVariant)
    {
        if($this->validateItem($productPartMaterialVariant)){
            $this->getProductPartMaterialVariants()->add($productPartMaterialVariant);
        }
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterial
     */
    public function getProductPartMaterial()
    {
        return $this->productPartMaterial;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductPartMaterialVariants()
    {
        return $this->productPartMaterialVariants;
    }
}

