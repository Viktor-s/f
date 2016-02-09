<?php

namespace Furniture\ProductBundle\Entity;

class ProductPartVariantSelection
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var ProductPart
     */
    protected $productPart;
    
    /**
     * @var ProductVariant
     */
    protected $productVariant;

    /**
     * @var ProductPartMaterialVariant 
     */
    protected $productPartMaterialVariant;
    
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
     * Get product part
     *
     * @return ProductPart
     */
    public function getProductPart()
    {
        return $this->productPart;
    }
    
    /**
     * Set product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductPartVariantSelection
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
     * Set product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return ProductPartVariantSelection
     */
    public function setProductVariant(ProductVariant $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }
    
    /**
     * Set product part material variant
     *
     * @param ProductPartMaterialVariant $material
     *
     * @return ProductPartVariantSelection
     */
    public function setProductPartMaterialVariant(ProductPartMaterialVariant $material)
    {
        $this->productPartMaterialVariant = $material;

        return $this;
    }
    
    /**
     * Get product part material variant
     *
     * @return ProductPartMaterialVariant
     */
    public function getProductPartMaterialVariant()
    {
        return $this->productPartMaterialVariant;
    }
}
