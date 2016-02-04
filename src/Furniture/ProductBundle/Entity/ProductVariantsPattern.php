<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Symfony\Component\Validator\Constraints as Assert;

class ProductVariantsPattern
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 0.01)
     */
    private $price;

    /**
     * @var ProductScheme
     *
     * @Assert\NotBlank(groups={"WithoutSchema"})
     */
    private $scheme;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var \Doctrine\Common\Collections\Collection|ProductPartPatternVariantSelection[]
     *
     * @Assert\Count(min = 1)
     */
    private $partPatternVariantSelections;

    /**
     * @var \Doctrine\Common\Collections\Collection|SkuOptionVariant[]
     */
    private $skuOptionValues;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->partPatternVariantSelections = new ArrayCollection();
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
     * Has part pattern variant selection?
     *
     * @param ProductPartPatternVariantSelection $selection
     *
     * @return bool
     */
    public function hasPartPatternVariantSelection(ProductPartPatternVariantSelection $selection)
    {
        if (!$selection->getId()) {
            // This is a new selection
            return false;
        }

        return $this->partPatternVariantSelections->exists(function ($key, ProductPartPatternVariantSelection $item) use ($selection) {
            return $item->getId() == $selection->getId();
        });
    }

    /**
     * Add part pattern variant selection.
     *
     * @param ProductPartPatternVariantSelection $selection
     *
     * @return ProductVariantsPattern
     */
    public function addPartPatternVariantSelection(ProductPartPatternVariantSelection $selection)
    {
        if (!$this->hasPartPatternVariantSelection($selection)) {
            $this->partPatternVariantSelections->add($selection);
        }

        return $this;
    }

    /**
     * Remove part pattern variant selection.
     *
     * @param ProductPartPatternVariantSelection $selection
     *
     * @return ProductVariantsPattern
     */
    public function removePartPatterVariantSelection(ProductPartPatternVariantSelection $selection)
    {
        $removalKey = null;

        $this->partPatternVariantSelections->forAll(function ($key, ProductPartPatternVariantSelection $item) use ($selection, &$removalKey) {
            if ($item->getId() == $selection->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if ($removalKey) {
            $this->partPatternVariantSelections->remove($removalKey);
        }

        return $this;
    }

    /**
     * Set part pattern variant selections.
     * Attention: this method not rewrite all part patterns! This method use diff and remove/insert elements.
     *
     * @param Collection|ProductPartPatternVariantSelection[] $selections
     *
     * @return ProductVariantsPattern
     */
    public function setPartPatternVariantSelections(Collection $selections)
    {
        // First step: add elements
        foreach ($selections as $selection) {
            $this->addPartPatternVariantSelection($selection);
        }

        // Second step: get selections for next remove.
        $removalSelections = [];
        foreach ($this->partPatternVariantSelections as $selection) {
            if (!$selection->getId()) {
                // This is a new selection. Not remove.
                continue;
            }

            $exist = $selections->exists(function ($key, ProductPartPatternVariantSelection $item) use ($selection) {
                return $item->getId() == $selection->getId();
            });

            if (!$exist) {
                $removalSelections[] = $selection;
            }
        }

        // Third step: remove selections
        foreach ($removalSelections as $selection) {
            $this->partPatternVariantSelections->removeElement($selection);
        }

        return $this;
    }

    /**
     * Get part variant selections
     *
     * @return \Doctrine\Common\Collections\Collection|ProductPartPatternVariantSelection[]
     */
    public function getPartPatternVariantSelections()
    {
        return $this->partPatternVariantSelections;
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

        if (null !== $removalKey) {
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
