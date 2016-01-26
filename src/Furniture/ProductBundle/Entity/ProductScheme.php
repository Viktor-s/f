<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class ProductScheme extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Collection|ProductVariant[]
     */
    private $productVariants;

    /**
     * @var Collection|ProductPart[]
     *
     * @Assert\Count(min = 1, minMessage="Please enter more then one product part for scheme.")
     */
    private $productParts;

    /**
     * @var Collection|ProductSchemeTranslation[]
     *
     * @Assert\Valid()
     */
    protected $translations;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->productVariants = new ArrayCollection();
        $this->productParts = new ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->translate()->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductScheme
     */
    public function setName($name)
    {
        $this->translate()->setName($name);

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
     * Set product
     *
     * @param Product $product
     *
     * @return ProductScheme
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Has product variants
     *
     * @return bool
     */
    public function hasProductVariants()
    {
        return (bool)$this->productVariants->isEmpty();
    }

    /**
     * Has product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return bool
     */
    public function hasProductVariant(ProductVariant $productVariant)
    {
        return $this->productVariants->contains($productVariant);
    }

    /**
     * Get product variants
     *
     * @return Collection|ProductVariant[]
     */
    public function getProductVariants()
    {
        return $this->productVariants;
    }

    /**
     * Set product variants
     *
     * @param Collection $productVariants
     *
     * @return ProductScheme
     */
    public function setProductVariants(Collection $productVariants)
    {
        $this->productVariants = $productVariants;

        return $this;
    }

    /**
     * Add product variant
     *
     * @param  ProductVariant $productVariant
     *
     * @return ProductScheme
     */
    public function addProductVariant(ProductVariant $productVariant)
    {
        if (!$this->hasProductVariant($productVariant)) {
            $this->productVariants->add($productVariant);
        }

        return $this;
    }

    /**
     * Remove product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return ProductScheme
     */
    public function removeProductVariant(ProductVariant $productVariant)
    {
        if ($this->hasProductVariant($productVariant)) {
            $this->productVariants->removeElement($productVariant);
        }

        return $this;
    }

    /**
     * Has product parts
     *
     * @return bool
     */
    public function hasProductParts()
    {
        return (bool)$this->productParts->isEmpty();
    }

    /**
     * Has product parts
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
     * Get product parts
     *
     * @return Collection|ProductPart[]
     */
    public function getProductParts()
    {
        return $this->productParts;
    }

    /**
     * Set product parts
     *
     * @param Collection $productParts
     *
     * @return ProductScheme
     */
    public function setProductParts(Collection $productParts)
    {
        $this->productParts = $productParts;

        return $this;
    }

    /**
     * Add product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductScheme
     */
    public function addProductPart(ProductPart $productPart)
    {
        if (!$this->hasProductPart($productPart)) {
            $this->productParts->add($productPart);
        }

        return $this;
    }

    /**
     * Remove product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductScheme
     */
    public function removeProductPart(ProductPart $productPart)
    {
        if ($this->hasProductPart($productPart)) {
            $this->productParts->removeElement($productPart);
        }

        return $this;
    }

}

