<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Validator\Constraints as Assert;

class FactoryRetailerRelation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     *
     * @Assert\NotBlank()
     */
    private $factory;
    
    /**
     * @var RetailerProfile
     */
    private $retailer;
    
    /**
     * @var bool
     */
    private $accessProducts = false;
    
    /**
     * @var bool
     */
    private $accessProductsPrices = false;
    
    /**
     * The discount for user in percentage
     *
     * @var int
     */
    private $discount = 0;
    
    /**
     * @var bool
     */
    private $active = true;

    /**
     * @var bool
     */
    private $retailerAccept;

    /**
     * @var bool
     */
    private $factoryAccept;

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
     * Set factory
     *
     * @param Factory $factory
     *
     * @return FactoryRetailerRelation
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
     * Set user
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryRetailerRelation
     */
    public function setRetailer(RetailerProfile $retailer)
    {
        $this->retailer = $retailer;

        return $this;
    }
    
    /**
     * Get user
     *
     * @return RetailerProfile
     */
    public function getRetailer()
    {
        return $this->retailer;
    }
    
    /**
     * Set right for access to products
     *
     * @param bool $status
     *
     * @return FactoryRetailerRelation
     */
    public function setAccessProducts($status)
    {
        $this->accessProducts = (bool)$status;

        return $this;
    }
    
    /**
     * Is access to products
     *
     * @return bool
     */
    public function isAccessProducts()
    {
        return $this->accessProducts;
    }
    
    /**
     * Set rights for access to product prices
     *
     * @param bool $status
     *
     * @return FactoryRetailerRelation
     */
    public function setAccessProductsPrices($status)
    {
        $this->accessProductsPrices = (bool)$status;

        return $this;
    }
    
    /**
     * Is rights for access to product prices
     *
     * @return bool
     */
    public function isAccessProductsPrices()
    {
        return $this->accessProductsPrices;
    }
    
    /**
     * Set discount
     *
     * @param int $percent
     *
     * @return FactoryRetailerRelation
     */
    public function setDiscount($percent)
    {
        $this->discount = (int)$percent;

        return $this;
    }
    
    /**
     * Get discount
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Is active relation
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Is not active relation?
     *
     * @return bool
     */
    public function isNotActive()
    {
        return !$this->isActive();
    }

    /**
     * Set active
     *
     * @param bool $status
     *
     * @return FactoryRetailerRelation
     */
    public function setActive($status)
    {
        $this->active = (bool) $status;

        return $this;
    }

    /**
     * Set retailer accept
     *
     * @param bool $accept
     *
     * @return FactoryRetailerRelation
     */
    public function setRetailerAccept($accept)
    {
        $this->retailerAccept = (bool) $accept;

        return $this;
    }

    /**
     * Is user accept
     *
     * @return bool
     */
    public function isRetailerAccept()
    {
        return $this->retailerAccept;
    }

    /**
     * Set factory accept
     *
     * @param bool $accept
     *
     * @return FactoryRetailerRelation
     */
    public function setFactoryAccept($accept)
    {
        $this->factoryAccept = (bool) $accept;

        return $this;
    }

    /**
     * Is user accept
     *
     * @return bool
     */
    public function isFactoryAccept()
    {
        return $this->factoryAccept;
    }
}
