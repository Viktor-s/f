<?php

namespace Furniture\ProductBundle\Pattern;

use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantSelectionCollection;

class ProductVariantParameters
{
    /**
     * @var ProductPartMaterialVariantSelectionCollection
     */
    private $materialVariants;

    /**
     * @var ProductScheme
     */
    private $productScheme;

    /**
     * Construct
     *
     * @param ProductPartMaterialVariantSelectionCollection $materialVariants
     * @param ProductScheme $scheme
     */
    public function __construct(
        ProductPartMaterialVariantSelectionCollection $materialVariants,
        ProductScheme $scheme = null
    )
    {
        $this->materialVariants = $materialVariants;
        $this->productScheme = $scheme;
    }

    /**
     * Get material variants
     *
     * @return ProductPartMaterialVariantSelectionCollection|\Furniture\ProductBundle\Model\ProductPartMaterialVariantSelection[]
     */
    public function getMaterialVariantSelections()
    {
        return $this->materialVariants;
    }

    /**
     * Get product scheme
     *
     * @return ProductScheme
     */
    public function getProductScheme()
    {
        return $this->productScheme;
    }
}
