<?php

namespace Furniture\SpecificationBundle\Model;

use Doctrine\Common\Collections\Collection;
use Furniture\FactoryBundle\Entity\Factory;

class GroupedItemsByFactory
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Collection|\Furniture\SpecificationBundle\Entity\SpecificationItem[]
     */
    private $items;

    /**
     * Construct
     *
     * @param Factory    $factory
     * @param Collection $items
     */
    public function __construct(Factory $factory, Collection $items)
    {
        $this->factory = $factory;
        $this->items = $items;
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
     * Get items
     *
     * @return Collection|\Furniture\SpecificationBundle\Entity\SpecificationItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
