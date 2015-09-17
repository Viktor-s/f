<?php

namespace Furniture\CompositionBundle\Entity;
use Furniture\ProductBundle\Entity\Product;

/**
 * This entity for create relation (Composite -> Product) for correct realize
 * ManyToMany association
 */
class CompositeProduct
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Composite
     */
    protected $composite;

    /**
     * @var CompositeTemplateItem
     */
    protected $templateItem;

    /**
     * @var Product
     */
    protected $product;

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
     * Set composite
     *
     * @param Composite $composite
     *
     * @return CompositeProduct
     */
    public function setComposite(Composite $composite)
    {
        $this->composite = $composite;

        return $this;
    }

    /**
     * Get composite
     *
     * @return Composite
     */
    public function getComposite()
    {
        return $this->composite;
    }

    /**
     * Set template item
     *
     * @param CompositeTemplateItem $item
     *
     * @return CompositeProduct
     */
    public function setTemplateItem(CompositeTemplateItem $item)
    {
        $this->templateItem = $item;

        return $this;
    }

    /**
     * Get template item
     *
     * @return CompositeTemplateItem
     */
    public function getTemplateItem()
    {
        return $this->templateItem;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return CompositeProduct
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
}
