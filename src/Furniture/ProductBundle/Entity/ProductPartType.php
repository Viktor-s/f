<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductPartType
{

    /**
     * @var int
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $code;

    /**
     * @var Collection
     */
    protected $productParts;

    /**
     * Construct
     */
    public function __construct()
    {
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProductPartType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set product parts
     *
     * @param Collection $productParts
     *
     * @return ProductPartType
     */
    public function setProductParts(Collection $productParts)
    {
        $this->productParts = $productParts;

        return $this;
    }

    /**
     * Get product parts
     *
     * @return Collection
     */
    public function getProductParts()
    {
        return $this->productParts;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString() 
    {
        return $this->getCode() ?: '';
    }
}
