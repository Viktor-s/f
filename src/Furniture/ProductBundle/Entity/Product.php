<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Validator\Constraint\ProductSchemesUnique;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\Product as BaseProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class Product extends BaseProduct
{
    const PRODUCT_SIMPLE = 1;
    const PRODUCT_SCHEMATIC = 2;

    /**
     * @var Collection
     */
    protected $subProducts;

    /**
     * @var Collection
     */
    protected $bundleProducts;

    /**
     * @var Collection|\Furniture\SkuOptionBundle\Entity\SkuOptionVariant[]
     */
    protected $skuOptionVariants;

    /**
     * @var Collection
     */
    protected $compositeCollections;

    /**
     * @var string
     */
    protected $factoryCode;

    /**
     * @var \Furniture\FactoryBundle\Entity\Factory
     *
     * @Assert\NotBlank()
     */
    protected $factory;

    /**
     * @var Collection|ProductPart[]
     *
     * @Assert\Valid()
     */
    protected $productParts;

    /**
     * @var ProductPdpConfig
     */
    protected $pdpConfig;

    /**
     * @var Collection|Category[]
     */
    protected $categories;

    /**
     * @var Collection|Type[]
     */
    protected $types;

    /**
     * @var Collection|Space[]
     */
    protected $spaces;

    /**
     * @var Collection|Style[]
     */
    protected $styles;

    /**
     * @var bool
     */
    private $availableForSale;

    /**
     * @var Collection|ProductScheme[]
     *
     * @Assert\Valid()
     * @Assert\Count(min = 2, minMessage="Please create at least two product schemes.", groups={"SchemesCreate"})
     * @ProductSchemesUnique(groups={"SchemesCreate"})
     */
    private $productSchemes;

    /**
     * @var integer
     */
    private $productType = self::PRODUCT_SIMPLE;

    /**
     * @var Collection|ProductTranslation[]
     *
     * @Assert\Valid()
     */
    protected $translations;

    /**
     * @var Collection|ProductVariantsPattern[]
     */
    private $productVariantsPatterns;

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
        $this->compositeCollections = new ArrayCollection();
        $this->productParts = new ArrayCollection();
        $this->productSchemes = new ArrayCollection();
        $this->productVariantsPatterns = new ArrayCollection();

        $this->categories = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->spaces = new ArrayCollection();
        $this->styles = new ArrayCollection();
    }

    /**
     * Has product parts?
     *
     * @return bool
     */
    public function hasProductParts()
    {
        return (bool)!$this->productParts->isEmpty();
    }

    /**
     * Get product parts
     *
     * @return Collection|ProductPart[]
     */
    public function getProductParts()
    {
        return $this->productParts;
    }

    /**
     *
     * @param Collection $productParts
     *
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setProductParts(Collection $productParts)
    {
        $this->productParts = $productParts;

        return $this;
    }

    /**
     * Has product path?
     *
     * @param ProductPart $productPart
     *
     * @return bool
     */
    public function hasProductPart(ProductPart $productPart)
    {
        return $this->productParts->contains($productPart);
    }


    /**
     * Add product part
     *
     * @param ProductPart $productPart
     *
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function addProductPart(ProductPart $productPart)
    {
        if (!$this->hasProductPart($productPart)) {
            $productPart->setProduct($this);
            $this->productParts[] = $productPart;
        }

        return $this;
    }

    /**
     * Remove product part
     *
     * @param ProductPart $productPart
     *
     * @return Product
     */
    public function removeProductPart(ProductPart $productPart)
    {
        if ($this->hasProductPart($productPart)) {
            $this->productParts->removeElement($productPart);
        }

        return $this;
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
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' ' . get_class($product) . ' must be belongable!');
        }

        if ($this->getId() && $this->getId() == $product->getId()) {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' ' . get_class($product) . ' cant contains it self!');
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
     * Get SKU option types
     *
     * @return Collection|\Furniture\SkuOptionBundle\Entity\SkuOptionType[]
     */
    public function getSkuOptionTypes()
    {
        $types = new ArrayCollection();

        foreach ($this->skuOptionVariants as $skuOptionVariant) {
            $type = $skuOptionVariant->getSkuOptionType();

            if (!$types->contains($type)) {
                $types->add($type);
            }
        }

        return $types;
    }

    /**
     * Get grouped SKU option variants
     *
     * @return array
     */
    public function getSkuOptionVariantsGrouped()
    {
        $grouped = [];

        $this->skuOptionVariants->forAll(function ($index, SkuOptionVariant $skuVariant) use (&$grouped) {
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
        if ($this->hasSkuOptionVariant($optionVariant)) {
            $this->skuOptionVariants->removeElement($optionVariant);
        }

        return $this;
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
     * Get paginated deleted variants
     *
     * @param int $page
     * @param int $limit
     *
     * @return Pagerfanta|ProductVariant[]
     */
    public function getPaginatedDeletedVariants($page = 1, $limit = 50)
    {
        $variants = $this->getDeletedVariants();
        $paginator = new Pagerfanta(new ArrayAdapter($variants->toArray()));

        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage($limit);

        return $paginator;
    }

    /**
     * Get paginated variants
     *
     * @param int $page
     * @param int $limit
     *
     * @return Pagerfanta|ProductVariant[]
     */
    public function getPaginatedVariants($page = 1, $limit = 50)
    {
        $variants = $this->getVariants();
        $variantsArray = $variants->toArray();
        // Sort by ID DESC
        usort($variantsArray, function ($a, $b) {
            /** @var ProductVariant $a */
            /** @var ProductVariant $b */
            return ($a->getId() > $b->getId()) ? -1 : 1;
        });
        $paginator = new Pagerfanta(new ArrayAdapter($variantsArray));
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage($limit);

        return $paginator;
    }

    /**
     * Get PDP config
     *
     * @return ProductPdpConfig
     */
    public function getPdpConfig()
    {
        if (!$this->pdpConfig) {
            $this->pdpConfig = new ProductPdpConfig();
            $this->pdpConfig->setProduct($this);
        }

        $this->fixPdpConfig();

        return $this->pdpConfig;
    }

    /**
     * Get categories
     *
     * @return Collection|Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Has category?
     *
     * @param Category $category
     *
     * @return bool
     */
    public function hasCategory(Category $category)
    {
        return $this->categories->exists(function ($key, Category $item) use ($category) {
            return $category->getId() == $item->getId();
        });
    }

    /**
     * Remove category
     *
     * @param Category $category
     *
     * @return Product
     */
    public function removeCategory(Category $category)
    {
        if ($this->hasCategory($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * Add category
     *
     * @param Category $category
     *
     * @return Product
     */
    public function addCategory(Category $category)
    {
        if (!$this->hasCategory($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Get spaces
     *
     * @return Collection|Space[]
     */
    public function getSpaces()
    {
        return $this->spaces;
    }

    /**
     * Has space
     *
     * @param Space $space
     *
     * @return bool
     */
    public function hasSpace(Space $space)
    {
        return $this->spaces->exists(function ($key, Space $item) use ($space) {
            return $item->getId() == $space->getId();
        });
    }

    /**
     * Remove space
     *
     * @param Space $space
     *
     * @return Product
     */
    public function removeSpace(Space $space)
    {
        if ($this->hasSpace($space)) {
            $this->spaces->removeElement($space);
        }

        return $this;
    }

    /**
     * Add space
     *
     * @param Space $space
     *
     * @return Product
     */
    public function addSpace(Space $space)
    {
        if (!$this->hasSpace($space)) {
            $this->spaces->add($space);
        }

        return $this;
    }

    /**
     * Get styles
     *
     * @return Collection|Style[]
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Has style?
     *
     * @param Style $style
     *
     * @return Product
     */
    public function hasStyle(Style $style)
    {
        return $this->styles->exists(function ($key, Style $item) use ($style) {
            return $item->getId() == $style->getId();
        });
    }

    /**
     * Remove style
     *
     * @param Style $style
     *
     * @return Product
     */
    public function removeStyle(Style $style)
    {
        if ($this->hasStyle($style)) {
            $this->styles->removeElement($style);
        }

        return $this;
    }

    /**
     * Add style
     *
     * @param Style $style
     *
     * @return Product
     */
    public function addStyle(Style $style)
    {
        if (!$this->hasStyle($style)) {
            $this->styles->add($style);
        }

        return $this;
    }

    /**
     * Get types
     *
     * @return Collection|Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Has type?
     *
     * @param Type $type
     *
     * @return bool
     */
    public function hasType(Type $type)
    {
        return $this->types->exists(function ($key, Type $item) use ($type) {
            return $type->getId() == $item->getId();
        });
    }

    /**
     * Remove type
     *
     * @param Type $type
     *
     * @return Product
     */
    public function removeType(Type $type)
    {
        if ($this->hasType($type)) {
            $this->types->removeElement($type);
        }

        return $this;
    }

    /**
     * Add type
     *
     * @param Type $type
     *
     * @return Product
     */
    public function addType(Type $type)
    {
        if (!$this->hasType($type)) {
            $this->types->add($type);
        }

        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getAvailableForSale()
    {
        return (bool)$this->availableForSale;
    }

    /**
     * Set available for sale status
     *
     * @param bool $availableForSale
     *
     * @return Product
     */
    public function setAvailableForSale($availableForSale)
    {
        $this->availableForSale = (bool)$availableForSale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        $now = new \DateTime();

        return ($now >= $this->availableOn && $this->getAvailableForSale());
    }

    /**
     * Get master image
     *
     * @return ProductVariantImage|null
     */
    public function getMasterImage()
    {
        if (!count($this->getImages())) {
            return null;
        }

        return $this->getImages()->first();
    }

    /**
     * Has product schemes
     *
     * @return bool
     */
    public function hasProductSchemes()
    {
        return !(bool)$this->productSchemes->isEmpty();
    }

    /**
     * Has product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return bool
     */
    public function hasProductScheme(ProductScheme $productScheme)
    {
        return $this->productSchemes->contains($productScheme);
    }

    /**
     * Grt product schemes
     *
     * @return Collection|ProductScheme[]
     */
    public function getProductSchemes()
    {
        return $this->productSchemes;
    }

    /**
     * Set product part schemes
     *
     * @param Collection $productSchemes
     *
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function setProductSchemes(Collection $productSchemes)
    {
        $this->productSchemes = $productSchemes;

        return $this;
    }

    /**
     * Add product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return Product
     */
    public function addProductScheme(ProductScheme $productScheme)
    {
        if (!$this->hasProductScheme($productScheme)) {
            $productScheme->setProduct($this);
            $this->productSchemes->add($productScheme);
        }

        return $this;
    }

    /**
     * Remove product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return Product
     */
    public function removeProductScheme(ProductScheme $productScheme)
    {
        if ($this->hasProductScheme($productScheme)) {
            $this->productSchemes->removeElement($productScheme);
        }

        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Product
     */
    public function setProductType($type)
    {
        $this->productType = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Is simple product?
     *
     * @return bool
     */
    public function isSimpleProductType()
    {
        return $this->productType === self::PRODUCT_SIMPLE;
    }

    /**
     * Is schematic product?
     *
     * @return bool
     */
    public function isSchematicProductType()
    {
        return $this->productType === self::PRODUCT_SCHEMATIC;
    }

    /**
     * Is not schematic product type?
     *
     * @return bool
     */
    public function isNotSchematicProductType()
    {
        return !$this->isSchematicProductType();
    }

    /**
     * Get product variant patterns
     *
     * @return Collection|ProductVariantsPattern[]
     */
    public function getProductVariantsPatterns()
    {
        return $this->productVariantsPatterns;
    }

    /**
     * Set product variant patterns
     *
     * @param Collection $productVariantsPatterns
     *
     * @return Product
     */
    public function setProductVariantsPatterns(Collection $productVariantsPatterns)
    {
        $this->productVariantsPatterns = $productVariantsPatterns;

        return $this;
    }

    /**
     * Has product variants patterns?
     *
     * @return bool
     */
    public function hasProductVariantsPatterns()
    {
        return !(bool)$this->productVariantsPatterns->isEmpty();
    }

    /**
     * Has product variant pattern?
     *
     * @param ProductVariantsPattern $productVariantsPattern
     *
     * @return bool
     */
    public function hasProductVariantsPattern(ProductVariantsPattern $productVariantsPattern)
    {
        return $this->productVariantsPatterns->contains($productVariantsPattern);
    }

    /**
     * Add product variant pattern
     *
     * @param ProductVariantsPattern $productVariantsPattern
     *
     * @return Product
     */
    public function addProductVariantsPattern(ProductVariantsPattern $productVariantsPattern)
    {
        if (!$this->hasProductVariantsPattern($productVariantsPattern)) {
            $productVariantsPattern->setProduct($this);
            $this->productVariantsPatterns->add($productVariantsPattern);
        }

        return $this;
    }

    /**
     * Remove product variant pattern
     *
     * @param ProductVariantsPattern $productVariantsPattern
     *
     * @return Product
     */
    public function removeProductVariantsPattern(ProductVariantsPattern $productVariantsPattern)
    {
        if ($this->hasProductVariantsPattern($productVariantsPattern)) {
            $this->productVariantsPatterns->removeElement($productVariantsPattern);
        }

        return $this;
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

    /**
     * Fix PDP config
     */
    private function fixPdpConfig()
    {
        //Add scheme input for schematic product
        if($this->isSchematicProductType()){
            $input = $this->pdpConfig->getInputForSchemes();

            if (!$input) {
                $input = new ProductPdpInput();
                $input->setForSchemes(true);
                $input->setPosition(0);
                $this->pdpConfig->addInput($input);
            }
        }

        // Add product parts
        foreach ($this->getProductParts() as $productPart) {
            $input = $this->pdpConfig->findInputForProductPart($productPart);

            if (!$input) {
                $input = new ProductPdpInput();
                $input->setProductPart($productPart);
                $input->setPosition(0);
                $this->pdpConfig->addInput($input);
            }
        }

        // Add sku option types
        foreach ($this->getSkuOptionTypes() as $skuOptionType) {
            $input = $this->pdpConfig->findInputForSkuOption($skuOptionType);

            if (!$input) {
                $input = new ProductPdpInput();
                $input->setSkuOption($skuOptionType);
                $input->setPosition(0);
                $this->pdpConfig->addInput($input);
            }
        }

        // Add options
        foreach ($this->getOptions() as $option) {
            $input = $this->pdpConfig->findInputForOption($option);

            if (!$input) {
                $input = new ProductPdpInput();
                $input->setOption($option);
                $input->setPosition(0);
                $this->pdpConfig->addInput($input);
            }
        }
    }
}
