<?php

namespace Furniture\ProductBundle\Twig;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\ProductRemoval\AttributeRemovalChecker;
use Furniture\ProductBundle\ProductRemoval\ProductRemovalChecker;
use Furniture\ProductBundle\ProductRemoval\SkuOptionTypeRemovalChecker;
use Furniture\ProductBundle\ProductRemoval\VariantRemovalChecker;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
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
     * @var SkuOptionTypeRemovalChecker
     */
    private $skuOptionRemovalChecker;

    /**
     * @var ProductRemovalChecker
     */
    private $productRemovalChecker;

    /**
     * Construct
     *
     * @param VariantRemovalChecker       $variantRemovalChecker
     * @param AttributeRemovalChecker     $attributeRemovalChecker
     * @param SkuOptionTypeRemovalChecker $skuOptionRemovalChecker
     * @param ProductRemovalChecker       $productRemovalChecker
     */
    public function __construct(
        VariantRemovalChecker $variantRemovalChecker,
        AttributeRemovalChecker $attributeRemovalChecker,
        SkuOptionTypeRemovalChecker $skuOptionRemovalChecker,
        ProductRemovalChecker $productRemovalChecker
    )
    {
        $this->variantRemovalChecker = $variantRemovalChecker;
        $this->attributeRemovalChecker = $attributeRemovalChecker;
        $this->skuOptionRemovalChecker = $skuOptionRemovalChecker;
        $this->productRemovalChecker = $productRemovalChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_product_variant_can_remove'      => new \Twig_Function_Method($this, 'isProductVariantCanRemove'),
            'is_product_variant_can_hard_remove' => new \Twig_Function_Method($this, 'isProductVariantCanHardRemove'),
            'is_product_attribute_can_remove'    => new \Twig_Function_Method($this, 'isProductAttributeCanRemove'),
            'is_sku_option_can_remove'           => new \Twig_Function_Method($this, 'isSkuOptionCanRemove'),
            'is_product_can_remove'              => new \Twig_Function_Method($this, 'isProductCanRemove'),
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
     * Is sku option can remove?
     *
     * @param SkuOptionType $skuOptionType
     *
     * @return bool
     */
    public function isSkuOptionCanRemove(SkuOptionType $skuOptionType)
    {
        return $this->skuOptionRemovalChecker->canRemove($skuOptionType)->canRemove();
    }

    /**
     * Is product can remove?
     *
     * @param Product $product
     *
     * @return bool
     */
    public function isProductCanRemove(Product $product)
    {
        return $this->productRemovalChecker->canRemove($product)->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product';
    }
}
