<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\ProductBundle\Entity\Pattern\AbstractProductVariantsPattern;
use Symfony\Component\Validator\Constraints as Assert;

class ProductVariantsPatternModifier extends AbstractProductVariantsPattern
{
    const ATTACH_TO_PRODUCT = 1;
    const ATTACH_TO_PATTERN = 2;

    /**
     * @var integer
     */
    private $attach;

    /**
     * @var ProductVariantsPattern
     *
     * @Assert\NotBlank(groups={"ModifierWithPattern"})
     */
    private $pattern;

    /**
     * @var string
     */
    private $price;

    /**
     * Set product variants pattern
     *
     * @param ProductVariantsPattern $pattern
     *
     * @return ProductVariantsPatternModifier
     */
    public function setPattern(ProductVariantsPattern $pattern = null)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get product variants pattern
     *
     * @return ProductVariantsPattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return ProductVariantsPatternModifier
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Get attach
     *
     * @return int
     */
    public function getAttach()
    {
        return $this->attach;
    }

    /**
     * Set attach
     *
     * @param int $attach
     *
     * @return ProductVariantsPatternModifier
     */
    public function setAttach($attach)
    {
        $this->attach = $attach;
    }

    /**
     * Is attached to product
     *
     * @return bool
     */
    public function isAttachedToProduct()
    {
        return $this->attach === self::ATTACH_TO_PRODUCT;
    }

    /**
     * Is attached to pattern
     *
     * @return bool
     */
    public function isAttachedToPattern()
    {
        return $this->attach === self::ATTACH_TO_PATTERN;
    }
}
