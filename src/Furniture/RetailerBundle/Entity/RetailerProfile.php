<?php
namespace Furniture\RetailerBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\GoogleServicesBundle\Entity\Interfaces\AddressMarkerInterface;

class RetailerProfile implements AddressMarkerInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Collection|RetailerUserProfile[]
     */
    private $retailerUserProfiles;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var array
     */
    private $emails = [];
    
    /**
     * @var RetailerProfileLogoImage
     */
    private $logoImage;

    /**
     *
     * @var str
     */
    private $website;
    
    /**
     *
     * @var str
     */
    private $subtitle;
        
    /**
     *
     * @var str
     */
    private $description;

    /**
     *
     * @var float
     */
    private $lat;
    
    /**
     *
     * @var float
     */
    private $lng;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;
    
    /**
     * Construct
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get users
     *
     * @return Collection|RetailerUserProfile[]
     */
    public function getRetailerUserProfiles()
    {
        return $this->retailerUserProfiles;
    }
    
    /**
     * 
     * @param Collection $retailerUserProfiles
     * @return \Furniture\RetailerBundle\Entity\RetailerUserProfile
     */
    public function setRetailerUserProfiles(Collection $retailerUserProfiles)
    {
        $this->retailerUserProfiles = $retailerUserProfiles;

        return $this;
    }
    
    /**
     * Has retailerUserProfiles?
     *
     * @return bool
     */
    public function hasRetailerUserProfiles()
    {
        return (bool)!$this->retailerUserProfiles->isEmpty();
    }
    
    /**
     * Has RetailerProfile?
     *
     * @param \Furniture\RetailerBundle\Entity\RetailerUserProfile $retailerUserProfiles
     *
     * @return bool
     */
    public function hasRetailerUserProfile(RetailerUserProfile $retailerUserProfiles)
    {
        return $this->retailerUserProfiles->contains($retailerUserProfiles);
    }


    /**
     * 
     * @param \Furniture\RetailerBundle\Entity\RetailerProfile $retailerUserProfiles
     * @return \Furniture\RetailerBundle\Entity\RetailerUserProfile
     */
    public function addRetailerUserProfile(RetailerUserProfile $retailerUserProfiles)
    {
        if(!$this->hasRetailerUserProfile($retailerUserProfiles)){
            $retailerUserProfiles->setRetailerProfile($this);
            $this->retailerUserProfiles->add($retailerUserProfiles);
        }

        return $this;
    }
    
    /**
     * Remove user
     *
     * @param User $user
     *
     * @return RetailerProfile
     */
    public function removeRetailerUserProfile(RetailerUserProfile $retailerUserProfiles)
    {
        if($this->hasRetailerUserProfile($retailerUserProfiles)){
            $this->retailerUserProfiles->removeElement($retailerUserProfiles);
        }

        return $this;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return RetailerProfile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set address
     *
     * @param string $address
     *
     * @return RetailerProfile
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }
    
    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * Set phones
     *
     * @param array $phones
     *
     * @return RetailerProfile
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;

        return $this;
    }
    
    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }
    
    /**
     * Set emails
     *
     * @param array $emails
     *
     * @return RetailerProfile
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
    }
    
    /**
     * Get emails
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }
    
    /**
     * Get logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * Remove logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function removeLogoImage()
    {
        $logoImage = $this->logoImage;
        $this->logoImage = null;

        return $logoImage;
    }
    
    /**
     * Set logo image
     *
     * @param RetailerProfileLogoImage $logoImage
     *
     * @return RetailerProfile
     */
    public function setLogoImage(RetailerProfileLogoImage $logoImage)
    {
        $logoImage->setRetailerProfile($this);
        $this->logoImage = $logoImage;

        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * 
     * @param str $website
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * 
     * @param str $subtitle
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * @param str $description
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * 
     * @return float
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * 
     * @return float
     */
    public function getLng() {
        return $this->lng;
    }

    /**
     * 
     * @param float $latitude
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setLat($latitude) {
        $this->lat = $latitude;
        return $this;
    }

    /**
     * 
     * @param float $longtitude
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setLng($longtitude) {
        $this->lng = $longtitude;
        return $this;
    }
    
    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * On update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

}
