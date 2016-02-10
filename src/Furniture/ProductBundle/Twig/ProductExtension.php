<?php

namespace Furniture\ProductBundle\Twig;

use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\ProductRemoval\AttributeRemovalChecker;
use Furniture\ProductBundle\ProductRemoval\VariantRemovalChecker;
use Sylius\Component\Product\Model\Attribute;

class ProductExtension extends \Twig_Extension
{
    /**
     * @var VariantRemovalChecker
     */
    private $variantRemovalChecker;

    /**
     * @var AttributeRemovalChecker
     */
    private $attributeRemovalChecker;

    /**
     * Construct
     *
     * @param VariantRemovalChecker $variantRemovalChecker
     * @param AttributeRemovalChecker $attributeRemovalChecker
     */
    public function __construct(
        VariantRemovalChecker $variantRemovalChecker,
        AttributeRemovalChecker $attributeRemovalChecker
    )
    {
        $this->variantRemovalChecker = $variantRemovalChecker;
        $this->attributeRemovalChecker = $attributeRemovalChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_product_variant_can_remove' => new \Twig_Function_Method($this, 'isProductVariantCanRemove'),
            'is_product_variant_can_hard_remove' => new \Twig_Function_Method($this, 'isProductVariantCanHardRemove'),

            'is_product_attribute_can_remove' => new \Twig_Function_Method($this, 'isProductAttributeCanRemove')
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
     * Is product attribute can remove?
     *
     * @param Attribute $attribute
     *
     * @return bool
     */
    public function isProductAttributeCanRemove(Attribute $attribute)
    {
        return $this->attributeRemovalChecker->canRemove($attribute)->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product';
    }
}
