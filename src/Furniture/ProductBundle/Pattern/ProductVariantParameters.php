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
     *
     * @var array
     */
    private $skuOptionVariantSelections;
    
    /**
     * Construct
     *
     * @param ProductPartMaterialVariantSelectionCollection $materialVariants
     * @param ProductScheme $scheme
     */
    public function __construct(
        ProductPartMaterialVariantSelectionCollection $materialVariants,
        ProductScheme $scheme = null,
        $skuOptionVariantSelections = null
    )
    {
        $this->materialVariants = $materialVariants;
        $this->productScheme = $scheme;
        $this->skuOptionVariantSelections = $skuOptionVariantSelections;
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
    
    /**
     * 
     * @return array[]|\Furniture\SkuOptionBundle\EntitySkuOptionVariant
     */
    public function getSkuOptionVariantSelections()
    {
        return $this->skuOptionVariantSelections;
    }
}
