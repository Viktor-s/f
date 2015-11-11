<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\FactoryBundle\Entity\Factory;

class CompositeCollectionQuery
{
    /**
     * @var array|int
     */
    private $ids;

    /**
     * @var array|int
     */
    private $factories;
    
    /**
     * With id
     *
     * @param int $id
     *
     * @return CompositeCollectionQuery
     */
    public function withId($id)
    {
        $this->ids[$id] = $id;

        return $this;
    }

    /**
     * With ids
     *
     * @param array $ids
     *
     * @return CompositeCollectionQuery
     */
    public function withIds(array $ids)
    {
        $this->ids = [];

        foreach ($ids as $id) {
            $this->withId($id);
        }

        return $this;
    }

    /**
     * Has ids?
     *
     * @return bool
     */
    public function hasIds()
    {
        return count($this->ids) > 0;
    }

    /**
     * Get ids
     *
     * @return array
     */
    public function getIds()
    {
        return array_values($this->ids);
    }

    /**
     * With factory
     *
     * @param Factory $factory
     *
     * @return CompositeCollectionQuery
     */
    public function withFactory(Factory $factory)
    {
        $this->factories[$factory->getId()] = $factory;

        return $this;
    }
    
    /**
     * With factories
     *
     * @param array|Factory[] $factories
     *
     * @return CompositeCollectionQuery
     */
    public function withFactories(array $factories)
    {
        $this->factories = $factories;

        return $this;
    }
    
    /**
     * Has factories?
     *
     * @return bool
     */
    public function hasFactories()
    {
        return count($this->factories) > 0;
    }
    
    /**
     * 
     * @return array
     */
    public function getFactories()
    {
        return array_values($this->factories);
    }
}
