<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RemovalCheckerRegistry
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get attribute removal chekcker
     *
     * @return AttributeRemovalChecker
     */
    public function getAttributeRemovalChecker()
    {
        return $this->container->get('product_attribute.removal_checker');
    }

    /**
     * Get product removal checker
     *
     * @return ProductRemovalChecker
     */
    public function getProductRemovalChecker()
    {
        return $this->container->get('product.removal_checker');
    }

    /**
     * Get product type removal checker
     *
     * @return ProductTypeRemovalChecker
     */
    public function getProductTypeRemovalChecker()
    {
        return $this->container->get('product_type.removal_checker');
    }

    /**
     * Get product space removal checker
     *
     * @return ProductSpaceRemovalChecker
     */
    public function getProductSpaceRemovalChecker()
    {
        return $this->container->get('product_space.removal_checker');
    }

    /**
     * Get product style removal checker
     *
     * @return ProductStyleRemovalChecker
     */
    public function getProductStyleRemovalChecker()
    {
        return $this->container->get('product_style.removal_checker');
    }

    /**
     * Get product category removal checker
     *
     * @return ProductCategoryRemovalChecker
     */
    public function getProductCategoryRemovalChecker()
    {
        return $this->container->get('product_category.removal_checker');
    }

    /**
     * Get sku options type removal checker
     *
     * @return SkuOptionTypeRemovalChecker
     */
    public function getSkuOptionTypeRemovalChecker()
    {
        return $this->container->get('sku_option.removal_checker');
    }

    /**
     * Get product variant removal checker
     *
     * @return VariantRemovalChecker
     */
    public function getProductVariantRemovalChecker()
    {
        return $this->container->get('product_variant.removal_checker');
    }

    /**
     * Get product part material option removal checker
     *
     * @return ProductPartMaterialOptionRemovalChecker
     */
    public function getProductPartMaterialOptionRemovalChecker()
    {
        return $this->container->get('product_part_material_option.removal_checker');
    }
}
