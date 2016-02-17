<?php

namespace Furniture\ProductBundle\Entity\PdpIntellectual;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\Product;

class CompositeExpression
{
    const TYPE_OR = 1;
    const TYPE_AND = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var CompositeExpression
     */
    private $parent;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var int
     */
    private $type;

    /**
     * @var Collection
     */
    private $child;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->child = new ArrayCollection();
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
     * Set parent
     *
     * @param CompositeExpression $compositeExpression
     *
     * @return CompositeExpression
     */
    public function setParent(CompositeExpression $compositeExpression = null)
    {
        $this->parent = $compositeExpression;

        return $this;
    }

    /**
     * Get parent
     *
     * @return CompositeExpression $compositeExpression
     */
    public function getParent()
    {
        return $this->parent;
    }
}
