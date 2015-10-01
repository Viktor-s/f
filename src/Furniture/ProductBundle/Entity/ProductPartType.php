<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductPartType {

    /**
     *
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

    function __construct() {
        $this->productParts = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * 
     * @param string $name
     * @return \Furniture\ProductBundle\Entity\ProductPartType
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $productParts
     * @return \Furniture\ProductBundle\Entity\ProductPartType
     */
    public function setProductParts(Collection $productParts) {
        $this->productParts = $productParts;
        return $this;
    }

    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductParts() {
        return $this->productParts;
    }

    public function __toString() 
    {
        return $this->getCode();
    }
    
}
