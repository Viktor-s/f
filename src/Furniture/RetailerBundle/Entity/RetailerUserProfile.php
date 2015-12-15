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
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var string
     */
    private $position;

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
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set user
     *
     * @param User $user
     *
     * @return RetailerUserProfile
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

    /**
     * Set contact phones
     *
     * @param array $phones
     *
     * @return RetailerUserProfile
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get contact phones
     *
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return RetailerUserProfile
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
}
