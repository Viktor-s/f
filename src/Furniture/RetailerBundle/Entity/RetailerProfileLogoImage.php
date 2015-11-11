<?php

namespace Furniture\RetailerBundle\Entity;

use Sylius\Component\Core\Model\Image;
use Furniture\CommonBundle\Uploadable\UploadableInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RetailerProfileLogoImage extends Image implements UploadableInterface
{
    /**
     * @var \SplFileInfo
     *
     * @Assert\Image()
     */
    protected $file;

    /**
     * @var RetailerProfile
     */
    private $retailerProfile;
    
    /**
     * @return RetailerProfile
     */
    public function getRetailerProfile()
    {
        return $this->retailerProfile;
    }
    
    /**
     * Set retailer profile
     *
     * @param RetailerProfile $retailerProfile
     *
     * @return RetailerProfileLogoImage
     */
    public function setRetailerProfile(RetailerProfile $retailerProfile)
    {
        $this->retailerProfile = $retailerProfile;

        return $this;
    }
}
