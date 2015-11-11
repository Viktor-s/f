<?php

namespace Furniture\FrontendBundle\Repository\Query;

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
     * 
     * @param array $factories
     * @return \Furniture\FrontendBundle\Repository\Query\CompositeCollectionQuery
     */
    public function withFactories(array $factories)
    {
        $this->factories = $factories;
        return $this;
    }
    
    /**
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
        return $this->factories;
    }
}
