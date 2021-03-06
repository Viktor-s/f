<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Sylius\Component\Core\Model\Image;
use Symfony\Component\Validator\Constraints as Assert;

class CustomSpecificationItemImage extends Image implements UploadableInterface
{
    /**
     * @var CustomSpecificationItem
     */
    private $customSpecificationItem;

    /**
     * @var \SplFileInfo
     *
     * @Assert\Image()
     */
    protected $file;

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
