<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Core\Model\ProductVariantImage as BaseProductVariantImage;

class ProductVariantImage extends BaseProductVariantImage
{
    /**
     * @var integer
     */
    protected $position = 0;

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return ProductVariantImage
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
