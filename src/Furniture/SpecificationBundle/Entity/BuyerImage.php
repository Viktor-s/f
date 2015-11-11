<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;
use Symfony\Component\Validator\Constraints as Assert;

class BuyerImage extends Image implements UploadableInterface
{
    /**
     * @var \SplFileInfo
     *
     * @Assert\Image()
     */
    protected $file;

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
