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
        $scheme = $this->getScheme();
        if ($scheme && $scheme->getProductParts()->count()) {
            $selectionArray = [];
            /** @var PersistentCollection $variantSelections */
            $variantSelections = $this->getPartPatternVariantSelections();
            /** @var PersistentCollection $schemeParts */
            $schemeParts = $this->getScheme()->getProductParts();
            /** @var ProductPartPatternVariantSelection $selection */
            foreach ($variantSelections as $selection) {
                if ($schemeParts->contains($selection->getProductPart())) {
                    $selectionArray[$selection->getProductPart()->getId()] = true;
                }
            }

            if ($scheme->getProductParts()->count() > count($selectionArray)) {
                $context->buildViolation('You should select at least one variant for each parts')
                    ->addViolation();
            }
        }

        // Validate SKU options.
        $productSkuOptions = $this->getProduct()->getSkuOptionVariantsGrouped();
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
                $context->buildViolation('You should select at least one SKU option for each groups')
                    ->addViolation();
            }
        }
    }
}
