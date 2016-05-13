<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Furniture\ProductBundle\Entity\Pattern\AbstractProductVariantsPattern;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ProductVariantsPattern
 * @package Furniture\ProductBundle\Entity
 * @Assert\Callback(callback="validate")
 */
class ProductVariantsPattern extends AbstractProductVariantsPattern
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 0.01)
     */
    private $price;

    /**
     * @var string
     */
    protected $factoryCode;
    
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
     * @return ProductVariantsPattern
     */
    public function setFactoryCode($code)
    {
        $this->factoryCode = $code;

        return $this;
    }
    
    /**
     * Get avtive active product code for this pattern
     * 
     * @return type
     */
    public function getActiveFactoryCode(){
        return $this->getFactoryCode() ? $this->getFactoryCode() : $this->getProduct()->getFactoryCode();
    }

    /**
     * Callback validation method.
     */
    public function validate(ExecutionContextInterface $context)
    {
        // Validate Product parts
        $error = false;
        $product = $this->getProduct();

        $productParts = new ArrayCollection();

        if ($product->isSimpleProductType()) {
            $productParts = $product->getProductParts();
        } elseif ($product->isSchematicProductType()) {
            $productParts = $this->getScheme()->getProductParts();
        }

        if ($productParts->count()) {
            $selectionArray = [];
            /** @var PersistentCollection $variantSelections */
            $variantSelections = $this->getPartPatternVariantSelections();
            /** @var ProductPartPatternVariantSelection $selection */
            foreach ($variantSelections as $selection) {
                if ($productParts->contains($selection->getProductPart())) {
                    $selectionArray[$selection->getProductPart()->getId()] = true;
                }
            }

            if ($productParts->count() > count($selectionArray)) {
                $error = true;
            }
        }

        // Validate SKU options.
        $productSkuOptions = $product->getSkuOptionVariantsGrouped();
        if (!empty($productSkuOptions)) {
            $selectionArray = [];
            /** @var ArrayCollection $optionSelections */
            $optionSelections = $this->getSkuOptionValues();
            /** @var SkuOptionVariant $selection */
            foreach ($optionSelections as $selection) {
                if (array_key_exists($selection->getSkuOptionType()->getId(), $productSkuOptions)) {
                    $selectionArray[$selection->getSkuOptionType()->getId()] = true;
                }
            }

            if (count($productSkuOptions) > count($selectionArray)) {
                $error = true;
            }
        }

        if ($error) {
            $context->buildViolation('You need to select at least one value for each option')
                ->addViolation();
        }
    }
}
