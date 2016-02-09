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
}
