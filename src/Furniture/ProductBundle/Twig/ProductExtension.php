<?php

namespace Furniture\ProductBundle\Twig;

use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\ProductRemoval\VariantRemovalChecker;

class ProductExtension extends \Twig_Extension
{
    /**
     * @var VariantRemovalChecker
     */
    private $variantRemovalChecker;

    /**
     * Construct
     *
     * @param VariantRemovalChecker $variantRemovalChecker
     */
    public function __construct(VariantRemovalChecker $variantRemovalChecker)
    {
        $this->variantRemovalChecker = $variantRemovalChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_product_variant_can_remove' => new \Twig_Function_Method($this, 'isProductVariantCanRemove'),
            'is_product_variant_can_hard_remove' => new \Twig_Function_Method($this, 'isProductVariantCanHardRemove')
        ];
    }

    /**
     * Is product variant can remove?
     *
     * @param ProductVariant $productVariant
     *
     * @return bool
     */
    public function isProductVariantCanRemove(ProductVariant $productVariant)
    {
        return $this->variantRemovalChecker->canRemove($productVariant)->canRemove();
    }

    /**
     * Is product variant can hard remove?
     *
     * @param ProductVariant $productVariant
     *
     * @return bool
     */
    public function isProductVariantCanHardRemove(ProductVariant $productVariant)
    {
        return $this->variantRemovalChecker->canHardRemove($productVariant)->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product';
    }
}
