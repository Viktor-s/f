<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;

class CustomSpecificationItemImage extends Image implements UploadableInterface
{
    /**
     * @var \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    private $customSpecificationItem;

    /**
     * Set buyer
     *
     * @param \Furniture\SpecificationBundle\Entity\CustomSpecificationItem $buyer
     *
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItemImage
     */
    public function setCustomSpecificationItem(CustomSpecificationItem $customSpecificationItem)
    {
        $this->customSpecificationItem = $customSpecificationItem;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function getCustomSpecificationItem()
    {
        return $this->customSpecificationItem;
    }
}
