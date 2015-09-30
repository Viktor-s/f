<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ProductVariant extends BaseProductVariant implements BaseVariantInterface
{
    /**
     * @var Collection
     */
    protected $skuOptions;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->skuOptions = new ArrayCollection();
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
