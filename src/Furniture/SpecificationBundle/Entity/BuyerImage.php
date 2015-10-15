<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;

class BuyerImage extends Image implements UploadableInterface
{
    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * Set buyer
     *
     * @param Buyer $buyer
     *
     * @return BuyerImage
     */
    public function setBuyer(Buyer $buyer)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return Buyer
     */
    public function getBuyer()
    {
        return $this->buyer;
    }
}
