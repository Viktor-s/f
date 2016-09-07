<?php

namespace Furniture\ProductBundle\Entity;


use Doctrine\Common\Collections\Collection;

class BestSellersSet
{
    /**
     * BestSellersSet id.
     *
     * @var integer
     */
    private $id;

    /**
     * @var Collection|BestSellers[]
     */
    private $bestSellers;

    /**
     * Is set is active?
     *
     * @var bool
     */
    private $active;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Has best sellers?
     *
     * @return bool
     */
    public function hasBestSellers()
    {
        return (bool)!$this->bestSellers->isEmpty();
    }

    /**
     * Get best sellers
     *
     * @return Collection|BestSellers[]
     */
    public function getBestSellers()
    {
        return $this->bestSellers;
    }

    /**
     * @param Collection $bestSellers
     *
     * @return BestSellersSet
     */
    public function setProductParts(Collection $bestSellers)
    {
        $this->bestSellers = $bestSellers;

        return $this;
    }

    /**
     * Has best seller?
     *
     * @param BestSellers $bestSeller
     *
     * @return bool
     */
    public function hasBestSeller(BestSellers $bestSeller)
    {
        return $this->bestSellers->contains($bestSeller);
    }


    /**
     * Add best seller
     *
     * @param BestSellers $bestSeller
     *
     * @return BestSellersSet
     */
    public function addProductPart(BestSellers $bestSeller)
    {
        if (!$this->hasBestSeller($bestSeller)) {
            $bestSeller->setBestSellerSet($this);
            $this->bestSellers->add($bestSeller);
        }

        return $this;
    }
}
