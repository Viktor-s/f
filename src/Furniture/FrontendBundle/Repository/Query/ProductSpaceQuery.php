<?php

namespace Furniture\FrontendBundle\Repository\Query;

class ProductSpaceQuery
{
    /**
     * @var array|int
     */
    private $ids;

    /**
     * With id
     *
     * @param int $id
     *
     * @return ProductSpaceQuery
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
     * @return ProductSpaceQuery
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
}
