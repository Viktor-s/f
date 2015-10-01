<?php

namespace Furniture\ProductBundle\Entity;

class ProductPartVariantSelection
{
    /**
     *
     * @var int
     */
    protected $id;
    
    /**
     *
     * @var ProductPart 
     */
    protected $productPart;
    
    /**
     *
     * @var ProductVariant 
     */
    protected $productVariant;
    
    
    /**
     *
     * @var ProductPartMaterialVariant 
     */
    protected $productPartMaterialVariant;
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return ProductPart
     */
    public function getProductPart()
    {
        return $this->productPart;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPart $productPart
     * @return \Furniture\ProductBundle\Entity\ProductPartVariantSelection
     */
    public function setProductPart(ProductPart $productPart)
    {
        $this->productPart = $productPart;
        return $this;
    }
    
    /**
     * 
     * @return ProductVariant
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductVariant $productVariant
     * @return \Furniture\ProductBundle\Entity\ProductPartVariantSelection
     */
    public function setProductVariant(ProductVariant $productVariant)
    {
        $this->productVariant = $productVariant;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialVariant $material
     * @return \Furniture\ProductBundle\Entity\ProductPartVariantSelection
     */
    public function setProductPartMaterialVariant(ProductPartMaterialVariant $material)
    {
        $this->productPartMaterialVariant = $material;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    public function getProductPartMaterialVariant()
    {
        return $this->productPartMaterialVariant;
    }
    
}

