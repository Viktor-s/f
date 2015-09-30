<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\PricingBundle\Model\PricingInterface;

class Product extends BaseProduct implements PricingInterface
{
    /**
     * @var Collection
     */
    protected $subProducts;

    /**
     * @var Collection
     */
    protected $bundleProducts;
    
    /**
     * @var Collection
     */
    protected $skuOptionVariants;

    /**
     * @var Collection
     */
    protected $extensions;

    /**
     * @var Collection
     */
    protected $compositeCollections;
    
    /**
     * @var string
     */
    protected $factoryCode;
    
    /**
     *
     * @var \Furniture\FactoryBundle\Entity\Factory
     */
    protected $factory;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->variants = new ArrayCollection();
        $this->setMasterVariant(new ProductVariant());

        $this->subProducts = new ArrayCollection();
        $this->bundleProducts = new ArrayCollection();
        $this->skuOptionVariants = new ArrayCollection();
        $this->extensions = new ArrayCollection();
        $this->compositeCollections = new ArrayCollection();
    }
    
    /**
     * Has sub products
     *
     * @return bool
     */
    public function hasSubProducts()
    {
        return (bool)!$this->subProducts->isEmpty();
    }
    
    /**
     * Is bundle?
     *
     * @return bool
     */
    public function isBundle()
    {
        if ($this->hasSubProducts()) {
            return true;
        }

        if ($this->bundleProducts->count()) {
            return false;
        }

        return false;
    }
    
    /**
     * Is belongable?
     *
     * @return bool
     */
    public function isBelongable()
    {
        if (!$this->bundleProducts->isEmpty()) {
            return true;
        }

        if (!$this->hasSubProducts()) {
            return true;
        }

        return true;
    }
    
    /**
     * Is simple?
     *
     * @return bool
     */
    public function isSimple()
    {
        if ($this->bundleProducts->isEmpty() && $this->subProducts->isEmpty()) {
            return true;
        }

        return false;
    }
    
    /**
     * Get sub products
     *
     * @return Collection
     */
    public function getSubProducts()
    {
        return $this->subProducts;
    }
    
    
    /**
     * Set sub products
     *
     * @param Collection $bundleProducts
     *
     * @return Product
     */
    public function setSubProducts(Collection $bundleProducts)
    {
        $this->subProducts = $bundleProducts;

        return $this;
    }
    
    /**
     * Has sub products
     *
     * @param Product $product
     *
     * @return bool
     */
    public function hasSubProduct(Product $product)
    {
        return $this->subProducts->contains($product);
    }
    
    /**
     * Add sub product
     *
     * @param Product $product
     *
     * @return Product
     *
     * @throws \Exception
     */
    public function addSubProduct(Product $product)
    {
        if (!$product->isBelongable()) {
            throw new \Exception(__CLASS__.'::'.__METHOD__.' '.get_class($product).' must be belongable!');
        }

        if ($this->getId() && $this->getId() == $product->getId()) {
            throw new \Exception(__CLASS__.'::'.__METHOD__.' '.get_class($product).' cant contains it self!');
        }

        if (!$this->hasSubProduct($product)) {
            $this->subProducts[] = $product;
        }
        
        return $this;
    }

    /**
     * Remove sub product
     *
     * @param Product $product
     *
     * @return Product
     */
    public function removeSubProduct(Product $product)
    {
        if ($this->hasSubProduct($product)) {
            $this->subProducts->removeElement($product);
        }

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
     * @return Product
     */
    public function setFactoryCode($code)
    {
        $this->factoryCode = $code;

        return $this;
    }
    
    /**
     * Has SKU option variants
     *
     * @return bool
     */
    public function hasSkuOptionVariants()
    {
        return (bool)!$this->skuOptionVariants->isEmpty();
    }
    
    /**
     * Get SKU option variants
     *
     * @return Collection
     */
    public function getSkuOptionVariants()
    {
        return $this->skuOptionVariants;
    }

    /**
     * Get grouped SKU option variants
     *
     * @return array
     */
    public function getSkuOptionVariantsGrouped()
    {
        $grouped = [];
        
        $this->skuOptionVariants->forAll(function ($id, SkuOptionVariant $skuVariant) use (&$grouped) {
            $optionId = $skuVariant->getSkuOptionType()->getId();

            if (!isset($grouped[$optionId])) {
                $grouped[$optionId] = [];
            }

            $grouped[$optionId][] = $skuVariant;

            return true;
        });
        
        return $grouped;
    }
    
    /**
     * Set SKU option variants
     *
     * @param Collection $variants
     *
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setSkuOptionVariants(Collection $variants)
    {
        $this->skuOptionVariants = $variants;

        return $this;
    }
    
    /**
     * Has SKU option variant
     *
     * @param SkuOptionVariant $optionVariant
     *
     * @return bool
     */
    public function hasSkuOptionVariant(SkuOptionVariant $optionVariant)
    {
        return $this->skuOptionVariants->contains($optionVariant);
    }
    
    /**
     * Add SKU option variant
     *
     * @param SkuOptionVariant $optionVariant
     *
     * @return Product
     */
    public function addSkuOptionVariant(SkuOptionVariant $optionVariant)
    {
        if (!$this->hasSkuOptionVariant($optionVariant)) {
            $optionVariant->setProduct($this);
            $this->skuOptionVariants[] = $optionVariant;
        }
        
        return $this;
    }
    
    /**
     * Remove SKU option variant
     *
     * @param SkuOptionVariant $optionVariant
     *
     * @return Product
     */
    public function removeSkuOptionVariant(SkuOptionVariant $optionVariant)
    {
        if($this->hasSkuOptionVariant($optionVariant)){
            $this->skuOptionVariants->removeElement($optionVariant);
        }

        return $this;
    }

    /**
     * Set extension variants
     *
     * @param Collection $extensions
     *
     * @return Product
     */
    public function setExtensions(Collection $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Get extension variants
     *
     * @return Collection|ProductExtension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Set composite collections
     *
     * @param Collection $collections
     *
     * @return Product
     */
    public function setCompositeCollections(Collection $collections)
    {
        $this->compositeCollections = $collections;

        return $this;
    }

    /**
     * Get composite collections
     *
     * @return Collection
     */
    public function getCompositeCollections()
    {
        return $this->compositeCollections;
    }
    
    /**
     * Set the factory
     *
     * @param Factory $factory
     *
     * @return Product
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
    
    /**
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get the product variant by sky
     *
     * @param string $skuId
     *
     * @return ProductVariant|null
     */
    public function getVariantById($skuId)
    {
        /** @var ProductVariant $productVariant */
        foreach ($this->variants as $productVariant) {
            if ($productVariant->getId() == $skuId) {
                return $productVariant;
            }
        }

        return null;
    }

    /**
     * Get deleted variants
     *
     * @return Collection|ProductVariant[]
     */
    public function getDeletedVariants()
    {
        return $this->variants->filter(function (ProductVariant $variant) {
            return $variant->isDeleted() && !$variant->isMaster();
        });
    }
    
    /**
     * Return translation model class.
     *
     * @return string
     */
    public static function getTranslationClass()
    {
        return get_parent_class(__CLASS__).'Translation';
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
