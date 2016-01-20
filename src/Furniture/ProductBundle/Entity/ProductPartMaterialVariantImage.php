<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Core\Model\Image;

class ProductPartMaterialVariantImage extends Image
{
    /**
     * @var ProductPartMaterialVariant
     */
    private $materialVariant;

    /**
     * Set product part material variant
     *
     * @param ProductPartMaterialVariant $materialVariant
     *
     * @return ProductPartMaterialVariantImage
     */
    public function setMaterialVariant(ProductPartMaterialVariant $materialVariant)
    {
        $this->materialVariant = $materialVariant;

        return $this;
    }

    /**
     * Get product part material variant
     *
     * @return ProductPartMaterialVariant
     */
    public function getMaterialVariant()
    {
        return $this->materialVariant;
    }
}
