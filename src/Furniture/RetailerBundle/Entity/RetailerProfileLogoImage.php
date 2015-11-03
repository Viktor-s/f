<?php
namespace Furniture\RetailerBundle\Entity;

use Sylius\Component\Core\Model\Image;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\CommonBundle\Uploadable\UploadableInterface;

class RetailerProfileLogoImage extends Image implements UploadableInterface
{
    /**
     *
     * @var \Furniture\RetailerBundle\Entity\RetailerProfile 
     */
    private $retailerProfile;
    
    /**
     * 
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function getRetailerProfile()
    {
        return $this->retailerProfile;
    }
    
    /**
     * 
     * @param \Furniture\RetailerBundle\Entity\RetailerProfile $retailerProfile
     * @return \Furniture\RetailerBundle\Entity\RetailerProfileLogoImage
     */
    public function setRetailerProfile(RetailerProfile $retailerProfile)
    {
        $this->retailerProfile = $retailerProfile;
        return $this;
    }
    
}

