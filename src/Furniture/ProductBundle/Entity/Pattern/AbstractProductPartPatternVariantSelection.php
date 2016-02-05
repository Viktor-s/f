<?php

namespace Furniture\ProductBundle\Entity\Pattern;

use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;

abstract class AbstractProductPartPatternVariantSelection
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var AbstractProductVariantsPattern
     */
    protected $pattern;

    /**
     * @var ProductPart
     */
    protected $productPart;

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
     * Set pattern
     *
     * @param AbstractProductVariantsPattern $pattern
     *
     * @return self
     */
    public function setPattern(AbstractProductVariantsPattern $pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return AbstractProductVariantsPattern
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
     * @return self
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
     * @return self
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
