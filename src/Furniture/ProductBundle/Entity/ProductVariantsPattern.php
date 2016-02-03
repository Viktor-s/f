<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;

class ProductVariantsPattern
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $price;

    /**
     * @var ProductScheme
     */
    private $scheme;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var \Doctrine\Common\Collections\Collection|ProductPartVariantSelection[]
     */
    private $partVariantSelections;

    /**
     * @var \Doctrine\Common\Collections\Collection|SkuOptionVariant[]
     */
    private $skuOptionValues;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->partVariantSelections = new ArrayCollection();
        $this->skuOptionValues = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductVariantsPattern
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param int $price
     *
     * @return ProductVariantsPattern
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set scheme
     *
     * @param ProductScheme $scheme
     *
     * @return ProductVariantsPattern
     */
    public function setScheme(ProductScheme $scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Get scheme
     *
     * @return ProductScheme
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return ProductVariantsPattern
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Has part variant selection?
     *
     * @param ProductPartVariantSelection $selection
     *
     * @return bool
     */
    public function hasPartVariantSelection(ProductPartVariantSelection $selection)
    {
        return $this->partVariantSelections->exists(function ($key, ProductPartVariantSelection $item) use ($selection) {
            return $item->getId() == $selection->getId();
        });
    }

    /**
     * Add part variant selection.
     *
     * @param ProductPartVariantSelection $selection
     *
     * @return ProductVariantsPattern
     */
    public function addPartVariantSelection(ProductPartVariantSelection $selection)
    {
        if (!$this->hasPartVariantSelection($selection)) {
            $this->partVariantSelections->add($selection);
        }

        return $this;
    }

    /**
     * Remove part variant selection.
     *
     * @param ProductPartVariantSelection $selection
     *
     * @return ProductVariantsPattern
     */
    public function removePartVariantSelection(ProductPartVariantSelection $selection)
    {
        $removalKey = null;

        $this->partVariantSelections->forAll(function ($key, ProductPartVariantSelection $item) use ($selection, &$removalKey) {
            if ($item->getId() == $selection->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if ($removalKey) {
            $this->partVariantSelections->remove($removalKey);
        }

        return $this;
    }

    /**
     * Get part variant selections
     *
     * @return \Doctrine\Common\Collections\Collection|ProductPartVariantSelection[]
     */
    public function getPartVariantSelections()
    {
        return $this->partVariantSelections;
    }

    /**
     * Has sku option value?
     *
     * @param SkuOptionVariant $variant
     *
     * @return bool
     */
    public function hasSkuOptionValue(SkuOptionVariant $variant)
    {
        return $this->skuOptionValues->exists(function ($key, SkuOptionVariant $item) use ($variant) {
            return $item->getId() == $variant->getId();
        });
    }

    /**
     * Add sku option value
     *
     * @param SkuOptionVariant $variant
     *
     * @return ProductVariantsPattern
     */
    public function addSkuOptionValue(SkuOptionVariant $variant)
    {
        if (!$this->hasSkuOptionValue($variant)) {
            $this->skuOptionValues->add($variant);
        }

        return $this;
    }

    /**
     * Remove sku option value
     *
     * @param SkuOptionVariant $variant
     *
     * @return ProductVariantsPattern
     */
    public function removeSkuOptionValue(SkuOptionVariant $variant)
    {
        $removalKey = null;

        $this->skuOptionValues->forAll(function ($key, SkuOptionVariant $item) use ($variant, &$removalKey) {
            if ($item->getId() == $variant->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if ($removalKey) {
            $this->skuOptionValues->remove($removalKey);
        }

        return $this;
    }

    /**
     * Get sku option values
     *
     * @return ProductVariantsPattern
     */
    public function getSkuOptionValues()
    {
        return $this->skuOptionValues;
    }
}
