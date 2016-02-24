<?php

namespace Furniture\ProductBundle\Twig;

use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductPartMaterialOption;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Type;
use Furniture\ProductBundle\ProductRemoval\RemovalCheckerRegistry;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Sylius\Component\Product\Model\Attribute;

class ProductExtension extends \Twig_Extension
{
    /**
     * @var RemovalCheckerRegistry
     */
    private $removalCheckerRegistry;

    /**
     * Construct
     *
     * @param RemovalCheckerRegistry $removalCheckerRegistry
     */
    public function __construct(RemovalCheckerRegistry $removalCheckerRegistry)
    {
        $this->removalCheckerRegistry = $removalCheckerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_product_variant_can_remove'              => new \Twig_Function_Method($this, 'isProductVariantCanRemove'),
            'is_product_variant_can_hard_remove'         => new \Twig_Function_Method($this, 'isProductVariantCanHardRemove'),
            'is_product_attribute_can_remove'            => new \Twig_Function_Method($this, 'isProductAttributeCanRemove'),
            'is_sku_option_can_remove'                   => new \Twig_Function_Method($this, 'isSkuOptionCanRemove'),
            'is_product_can_remove'                      => new \Twig_Function_Method($this, 'isProductCanRemove'),
            'is_product_type_can_remove'                 => new \Twig_Function_Method($this, 'isProductTypeCanRemove'),
            'is_product_space_can_remove'                => new \Twig_Function_Method($this, 'isProductSpaceCanRemove'),
            'is_product_style_can_remove'                => new \Twig_Function_Method($this, 'isProductStyleCanRemove'),
            'is_product_category_can_remove'             => new \Twig_Function_Method($this, 'isProductCategoryCanRemove'),
            'is_product_part_material_option_can_remove' => new \Twig_Function_Method($this, 'isProductPartMaterialOptionCanRemove'),
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
        return $this->removalCheckerRegistry
            ->getProductVariantRemovalChecker()
            ->canRemove($productVariant)
            ->canRemove();
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
        return $this->removalCheckerRegistry
            ->getProductVariantRemovalChecker()
            ->canHardRemove($productVariant)
            ->canRemove();
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
        return $this->removalCheckerRegistry
            ->getAttributeRemovalChecker()
            ->canRemove($attribute)
            ->canRemove();
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
        return $this->removalCheckerRegistry
            ->getSkuOptionTypeRemovalChecker()
            ->canRemove($skuOptionType)
            ->canRemove();
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
        return $this->removalCheckerRegistry
            ->getProductRemovalChecker()
            ->canRemove($product)
            ->canRemove();
    }

    /**
     * Is product type can remove?
     *
     * @param Type $type
     *
     * @return bool
     */
    public function isProductTypeCanRemove(Type $type)
    {
        return $this->removalCheckerRegistry
            ->getProductTypeRemovalChecker()
            ->canHardRemove($type)
            ->canRemove();
    }

    /**
     * Is product space can remove?
     *
     * @param Space $space
     *
     * @return bool
     */
    public function isProductSpaceCanRemove(Space $space)
    {
        return $this->removalCheckerRegistry
            ->getProductSpaceRemovalChecker()
            ->canHardRemove($space)
            ->canRemove();
    }

    /**
     * Is product style can remove?
     *
     * @param Style $style
     *
     * @return bool
     */
    public function isProductStyleCanRemove(Style $style)
    {
        return $this->removalCheckerRegistry
            ->getProductStyleRemovalChecker()
            ->canHardRemove($style)
            ->canRemove();
    }

    /**
     * Is product category can remove?
     *
     * @param Category $category
     *
     * @return bool
     */
    public function isProductCategoryCanRemove(Category $category)
    {
        return $this->removalCheckerRegistry
            ->getProductCategoryRemovalChecker()
            ->canHardRemove($category)
            ->canRemove();
    }

    /**
     * Is product part material option can remove?
     *
     * @param ProductPartMaterialOption $option
     *
     * @return bool
     */
    public function isProductPartMaterialOptionCanRemove(ProductPartMaterialOption $option)
    {
        return $this->removalCheckerRegistry
            ->getProductPartMaterialOptionRemovalChecker()
            ->canHardRemove($option)
            ->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product';
    }
}
