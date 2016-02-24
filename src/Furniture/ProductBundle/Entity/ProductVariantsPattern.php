<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\Pattern\AbstractProductVariantsPattern;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Symfony\Component\Validator\Constraints as Assert;

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
    
    
}
