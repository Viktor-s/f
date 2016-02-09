<?php

namespace Furniture\ProductBundle\Model;

use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;

class ProductPartMaterialVariantSelection
{
    /**
     * @var ProductPart
     */
    private $productPart;

    /**
     * @var ProductPartMaterialVariant
     */
    private $materialVariant;

    /**
     * Construct
     *
     * @param ProductPart $productPart
     */
    public function __construct(ProductPart $productPart, ProductPartMaterialVariant $materialVariant)
    {
        $this->productPart = $productPart;
        $this->materialVariant = $materialVariant;
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
     * Get material variant
     *
     * @return ProductPartMaterialVariant $materialVariant
     */
    public function getMaterialVariant()
    {
        return $this->materialVariant;
    }
}
