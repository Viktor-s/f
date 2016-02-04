<?php

namespace Furniture\ProductBundle\Entity;

class ProductPartPatternVariantSelection
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductVariantsPattern
     */
    private $pattern;

    /**
     * @var ProductPart
     */
    private $productPart;

    /**
     * @var ProductPartMaterialVariant
     */
    private $productPartMaterialVariant;

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
     * Set pattern
     *
     * @param ProductVariantsPattern $pattern
     *
     * @return ProductPartPatternVariantSelection
     */
    public function setPattern(ProductVariantsPattern $pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return ProductVariantsPattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductPartPatternVariantSelection
     */
    public function setProductPart(ProductPart $productPart)
    {
        $this->productPart = $productPart;

        return $this;
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
     * Set product part material variant
     *
     * @param ProductPartMaterialVariant $materialVariant
     *
     * @return ProductPartPatternVariantSelection
     */
    public function setProductPartMaterialVariant(ProductPartMaterialVariant $materialVariant = null)
    {
        $this->productPartMaterialVariant = $materialVariant;

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
