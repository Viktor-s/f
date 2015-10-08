<?php

namespace Furniture\FactoryBundle\Entity;

class FactoryDefaultRelation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var bool
     */
    private $accessProducts = true;

    /**
     * @var bool
     */
    private $accessProductsPrices = true;

    /**
     * Construct
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
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
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set access products
     *
     * @param bool $access
     *
     * @return FactoryDefaultRelation
     */
    public function setAccessProducts($access)
    {
        $this->accessProducts = (bool) $access;

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
     * Set access to product prices
     *
     * @param bool $access
     *
     * @return FactoryDefaultRelation
     */
    public function setAccessProductsPrices($access)
    {
        $this->accessProductsPrices = $access;

        return $this;
    }

    /**
     * Is access to product prices
     *
     * @return bool
     */
    public function isAccessProductsPrices()
    {
        return $this->accessProductsPrices;
    }
}
