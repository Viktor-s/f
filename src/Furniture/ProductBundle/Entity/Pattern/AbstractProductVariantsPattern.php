<?php

namespace Furniture\ProductBundle\Entity\Pattern;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\Product;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;

abstract class AbstractProductVariantsPattern
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var ProductScheme
     *
     * @Assert\NotBlank(groups={"WithoutSchema"})
     */
    protected $scheme;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var \Doctrine\Common\Collections\Collection|ProductPartPatternVariantSelection[]
     *
     * @Assert\Count(min = 1)
     */
    protected $partPatternVariantSelections;

    /**
     * @var \Doctrine\Common\Collections\Collection|SkuOptionVariant[]
     */
    protected $skuOptionValues;

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
     * @return self
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
     * Set scheme
     *
     * @param ProductScheme $scheme
     *
     * @return self
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
     * @return self
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
     * @param AbstractProductPartPatternVariantSelection $selection
     *
     * @return bool
     */
    public function hasPartPatternVariantSelection(AbstractProductPartPatternVariantSelection $selection)
    {
        if (!$selection->getId()) {
            // This is a new selection
            return false;
        }

        return $this->partPatternVariantSelections->exists(function ($key, AbstractProductPartPatternVariantSelection $item) use ($selection) {
            return $item->getId() == $selection->getId();
        });
    }

    /**
     * Add part pattern variant selection.
     *
     * @param AbstractProductPartPatternVariantSelection $selection
     *
     * @return self
     */
    public function addPartPatternVariantSelection(AbstractProductPartPatternVariantSelection $selection)
    {
        if (!$this->hasPartPatternVariantSelection($selection)) {
            $this->partPatternVariantSelections->add($selection);
        }

        return $this;
    }

    /**
     * Remove part pattern variant selection.
     *
     * @param AbstractProductPartPatternVariantSelection $selection
     *
     * @return self
     */
    public function removePartPatterVariantSelection(AbstractProductPartPatternVariantSelection $selection)
    {
        $removalKey = null;

        $this->partPatternVariantSelections->forAll(function ($key, AbstractProductPartPatternVariantSelection $item) use ($selection, &$removalKey) {
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
     * @param Collection|AbstractProductPartPatternVariantSelection[] $selections
     *
     * @return self
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

            $exist = $selections->exists(function ($key, AbstractProductPartPatternVariantSelection $item) use ($selection) {
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
     * @return \Doctrine\Common\Collections\Collection|AbstractProductPartPatternVariantSelection[]
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function getSkuOptionValues()
    {
        return $this->skuOptionValues;
    }
}
