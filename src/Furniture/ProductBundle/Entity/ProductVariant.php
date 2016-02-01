<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\ProductBundle\Validator\Constraint\ProductVariant as ProductVariantConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ProductVariantConstraint
 */
class ProductVariant extends BaseProductVariant implements BaseVariantInterface
{
    /**
     * @var Collection|SkuOptionVariant[]
     */
    protected $skuOptions;

    /**
     * @var Collection|ProductPartVariantSelection[]
     */
    protected $productPartVariantSelections;

    /**
     * @var ProductScheme
     */
    private $productScheme;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     */
    protected $price;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->skuOptions = new ArrayCollection();
        $this->productPartVariantSelections = new ArrayCollection();
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return parent::getProduct();
    }

    /**
     * Has SKU options?
     *
     * @return bool
     */
    public function hasSkuOptions()
    {
        return (bool)!$this->skuOptions->isEmpty();
    }

    /**
     * Get SKU options
     *
     * @return Collection|SkuOptionVariant[]
     */
    public function getSkuOptions()
    {
        return $this->skuOptions;
    }

    /**
     * Set SKU options
     *
     * @param Collection $skuOptions
     *
     * @return ProductVariant
     */
    public function setSkuOptions(Collection $skuOptions)
    {
        $this->skuOptions = $skuOptions;

        return $this;
    }

    /**
     * Has SKU option variant?
     *
     * @param SkuOptionVariant $skuOptionVariant
     *
     * @return bool
     */
    public function hasSkuOption(SkuOptionVariant $skuOptionVariant)
    {
        return $this->skuOptions->contains($skuOptionVariant);
    }

    /**
     * Add SKU option
     *
     * @param SkuOptionVariant $skuOptionVariant
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function addSkuOption(SkuOptionVariant $skuOptionVariant)
    {
        if (!$this->hasSkuOption($skuOptionVariant)) {
            $this->skuOptions[] = $skuOptionVariant;
        }

        return $this;
    }

    /**
     * Remove SKU option variant
     *
     * @param SkuOptionVariant $skuOptionVariant
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function removeSkuOption(SkuOptionVariant $skuOptionVariant)
    {
        if ($this->hasSkuOption($skuOptionVariant)) {
            $this->skuOptions->removeElement($skuOptionVariant);
        }

        return $this;
    }

    /**
     * Set product part variant selections
     *
     * @param Collection|ProductPartVariantSelection[] $variantSelections
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function setProductPartVariantSelections(Collection $variantSelections)
    {
        $this->productPartVariantSelections = $variantSelections;

        return $this;
    }

    /**
     * Get product part variant selections
     *
     * @return Collection|ProductPartVariantSelection[]
     */
    public function getProductPartVariantSelections()
    {
        return $this->productPartVariantSelections;
    }

    /**
     * Has SKU options?
     *
     * @return bool
     */
    public function hasProductPartVariantSelections()
    {
        return (bool)!$this->productPartVariantSelections->isEmpty();
    }

    /**
     *
     * @param ProductPartVariantSelection $variantSelection
     *
     * @return bool
     */
    public function hasProductPartVariantSelection(ProductPartVariantSelection $variantSelection)
    {
        return $this->productPartVariantSelections->contains($variantSelection);
    }

    /**
     * Add ProductPartVariantSelection option
     *
     * @param ProductPartVariantSelection $variantSelection
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function addProductPartVariantSelection(ProductPartVariantSelection $variantSelection)
    {
        if (!$this->hasProductPartVariantSelection($variantSelection)) {
            $variantSelection->setProductVariant($this);
            $this->productPartVariantSelections[] = $variantSelection;
        }

        return $this;
    }

    /**
     * Remove ProductPartVariantSelection option
     *
     * @param ProductPartVariantSelection $variantSelection
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function removeProductPartVariantSelection(ProductPartVariantSelection $variantSelection)
    {
        if ($this->hasProductPartVariantSelection($variantSelection)) {
            $this->productPartVariantSelections->removeElement($variantSelection);
        }

        return $this;
    }

    /**
     *
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function getProductScheme()
    {
        return $this->productScheme;
    }

    /**
     *
     * @param ProductScheme $productScheme
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     */
    public function setProductScheme(ProductScheme $productScheme)
    {
        $this->productScheme = $productScheme;

        return $this;
    }


    /**
     * Get human size
     *
     * @return string
     */
    public function getHumanSize()
    {
        if ($this->width && $this->height && $this->depth) {
            return sprintf(
                '%sx%sx%s',
                $this->width,
                $this->height,
                $this->depth
            );
        }

        return '';
    }
}
