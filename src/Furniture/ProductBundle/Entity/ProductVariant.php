<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\ProductBundle\Validator\Constraint\ProductVariant as ProductVariantConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ProductVariantConstraint
 * @Assert\Callback(callback="validate", groups={"CreateProductVariant"})
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
     * @var string
     */
    protected $factoryCode;

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
     * Get factory code
     *
     * @return string
     */
    public function getFactoryCode()
    {
        return $this->factoryCode;
    }

    /**
     * Set factory code
     *
     * @param string $code
     *
     * @return ProductVariant
     */
    public function setFactoryCode($code)
    {
        $this->factoryCode = $code;

        return $this;
    }

    /**
     * Get avtive active product code for this product variant
     *
     * @return type
     */
    public function getActiveFactoryCode(){
        return $this->getFactoryCode() ? $this->getFactoryCode() : $this->getProduct()->getFactoryCode();
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

    /**
     * Validate ProductVariant
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        /** @var Product $product */
        $product = $this->getProduct();
        /** @var Collection $selections */
        $selections = $this->getProductPartVariantSelections();
        $skuOptionSelections = $this->getSkuOptions();

        $productParts = null;
        $skuOptions = null;

        // Validate ProductVariantSelection
        if ($product->isSchematicProductType() && !empty($this->getProductScheme())) {
            if ($this->getProductScheme()->getProductParts()->count() > $this->getProductPartVariantSelections()->count(
                )
            ) {
                $productParts = $this->getProductScheme()->getProductParts();
            }
        } else if ($product->isSimpleProductType()) {
            if ($product->getProductParts()->count() > $this->getProductPartVariantSelections()->count()) {
                $productParts = $product->getProductParts();
            }
        }

        if (count($product->getSkuOptionVariantsGrouped()) > $this->getSkuOptions()->count()) {
            $skuOptions = $product->getSkuOptionVariantsGrouped();
        }

        if ($productParts) {
            $productParts->forAll(
                function ($key, $element) use ($selections, $context) {
                    /** @var ProductPart $element */
                    /** @var ProductPartVariantSelection $selection */
                    $hasProductPart = false;
                    foreach ($selections as $selection) {
                        if ($selection->getProductPart() === $element) {
                            $hasProductPart = true;
                        }
                    }

                    if (!$hasProductPart || $selections->isEmpty()) {
                        $context->buildViolation('Product variant options should not be empty.')
                            ->atPath(sprintf('productPartVariantSelections[%d]', $element->getId()))
                            ->addViolation();
                    }

                    return true;
                }
            );
        }

        if ($skuOptions) {
            $skuOptions = new ArrayCollection($skuOptions);
            $skuOptions->forAll(
                function ($key, $elements) use ($skuOptionSelections, $context) {
                    /** @var SkuOptionVariant[] $elements */
                    /** @var Collection<SkuOptionVariant> $skuOptionSelections */

                    $hasSkuOption = false;
                    foreach ($skuOptionSelections as $skuOption) {
                        if (in_array($skuOption, $elements, true)) {
                            $hasSkuOption = true;
                        }
                    }

                    if (!$hasSkuOption || $skuOptionSelections->isEmpty()) {
                        $context->buildViolation('Product sku options should not be empty.')
                            ->atPath(sprintf('skuOptions[%d]', $key))
                            ->addViolation();
                    }

                    return true;
                }
            );
        }
    }
}
