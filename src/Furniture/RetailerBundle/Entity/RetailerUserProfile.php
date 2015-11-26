<?php

namespace Furniture\RetailerBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerUserProfile 
{
    
    const RETAILER_ADMIN    = 1;
    const RETAILER_EMPLOYEE = 2;
    
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var RetailerProfile
     */
    protected $retailerProfile;
    
    /**
     * @var int
     */
    private $retailerMode = self::RETAILER_EMPLOYEE;
    
    /**
     *
     * @var \Furniture\CommonBundle\Entity\User
     */
    private $user;


    /**
     * Construct
     */
    public function __construct()
    {
        
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
     * Set retail profile
     *
     * @param RetailerProfile $retailerProfile
     *
     * @return User
     */
    public function setRetailerProfile(RetailerProfile $retailerProfile)
    {
        $this->retailerProfile = $retailerProfile;
        return $this;
    }

    /**
     * Get retail profile
     *
     * @return RetailerProfile
     */
    public function getRetailerProfile()
    {
        return $this->retailerProfile;
    }

    /**
     * Set retailer mode
     *
     * @param int $retailerMode
     *
     * @return User
     */
    public function setRetailerMode($retailerMode)
    {
        $this->retailerMode = $retailerMode;

        return $this;
    }

    /**
     * Get retailer mode
     *
     * @return int
     */
    public function getRetailerMode()
    {
        return $this->retailerMode;
    }
    
    /**
     * 
     * @return \Furniture\CommonBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * 
     * @param \Furniture\CommonBundle\Entity\User $user
     * @return \Furniture\RetailerBundle\Entity\RetailerUserProfile
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
    
    /**
     * Is retailer admin?
     *
     * @return bool
     */
    public function isRetailerAdmin()
    {
        return $this->retailerMode == self::RETAILER_ADMIN;
    }

    /**
     * Is retail employee
     *
     * @return bool
     */
    public function isRetailerEmployee()
    {
        return $this->retailerMode == self::RETAILER_EMPLOYEE;
    }
    
}

