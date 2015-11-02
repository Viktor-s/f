<?php

namespace Furniture\SpecificationBundle\Model;

use Doctrine\Common\Collections\Collection;

class GroupedCustomItemsByFactory
{
    /**
     * @var string
     */
    private $factoryName;

    /**
     * @var Collection|\Furniture\SpecificationBundle\Entity\CustomSpecificationItem[]
     */
    private $items;

    /**
     * Construct
     *
     * @param string     $factoryName
     * @param Collection $items
     */
    public function __construct($factoryName, Collection $items)
    {
        $this->factoryName = $factoryName;
        $this->items = $items;
    }

    /**
     * Get factory name
     *
     * @return string
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }

    /**
     * Get items
     *
     * @return Collection|\Furniture\SpecificationBundle\Entity\SpecificationItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}