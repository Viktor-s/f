<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;

class CustomSpecificationItemImage extends Image implements UploadableInterface
{
    /**
     * @var CustomSpecificationItem
     */
    private $customSpecificationItem;

    /**
     * Set custom specification item
     *
     * @param CustomSpecificationItem $customSpecificationItem
     *
     * @return CustomSpecificationItemImage
     */
    public function setCustomSpecificationItem(CustomSpecificationItem $customSpecificationItem)
    {
        $this->customSpecificationItem = $customSpecificationItem;

        return $this;
    }

    /**
     * Get custom specification item
     *
     * @return CustomSpecificationItem
     */
    public function getCustomSpecificationItem()
    {
        return $this->customSpecificationItem;
    }
}
