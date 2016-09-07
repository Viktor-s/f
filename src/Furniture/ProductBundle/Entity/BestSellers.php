<?php

namespace Furniture\ProductBundle\Entity;


class BestSellers
{
    /**
     * BestSeller id.
     *
     * @var integer
     */
    private $id;

    /**
     * Item position in set
     *
     * @var integer
     */
    private $position;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var BestSellersSet
     */
    private $bestSellerSet;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return BestSellersSet
     */
    public function getBestSellersSet()
    {
        return $this->bestSellerSet;
    }

    /**
     * @param BestSellersSet $bestSellerSet
     */
    public function setBestSellerSet(BestSellersSet $bestSellerSet)
    {
        $this->bestSellerSet = $bestSellerSet;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }
}
