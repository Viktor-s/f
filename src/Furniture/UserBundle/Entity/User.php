<?php

namespace Furniture\UserBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Component\Core\Model\User as BaseUser;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

class User extends BaseUser 
{
    const ROLE_CONTENT_USER   = 'ROLE_CONTENT_USER'; // @todo: remove this role?
    const ROLE_FACTORY_ADMIN  = 'ROLE_FACTORY_ADMIN';
    const ROLE_FACTORY_USER   = 'ROLE_FACTORY_USER';
    const ROLE_PUBLIC_CONTENT = 'ROLE_PUBLIC_CONTENT';

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var RetailerUserProfile
     */
    protected $retailerUserProfile;

    /**
     * @var bool
     */
    protected $shouldControlForKill = false;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set factory
     *
     * @param Factory $factory
     *
     * @return User
     */
    public function setFactory(Factory $factory = null)
    {
        $this->resetProfile();
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get factory
     *
     * @return Factory|null
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Has factory
     *
     * @return bool
     */
    public function hasFactory()
    {
        return $this->factory ? true : false;
    }

    /**
     * Should control for kill
     *
     * @return User
     */
    public function shouldControlForKill()
    {
        $this->shouldControlForKill = true;

        return $this;
    }

    /**
     * Is should control for kill?
     *
     * @return bool
     */
    public function isShouldControlForKill()
    {
        return $this->shouldControlForKill;
    }

    /**
     * Get retailer user profile
     *
     * @return RetailerUserProfile
     */
    public function getRetailerUserProfile()
    {
        return $this->retailerUserProfile;
    }
    
    /**
     * Set retailer user profile
     *
     * @param RetailerUserProfile $retailerUserProfile
     *
     * @return User
     */
    public function setRetailerUserProfile(RetailerUserProfile $retailerUserProfile)
    {
        $this->resetProfile();
        $retailerUserProfile->setUser($this);
        $this->retailerUserProfile = $retailerUserProfile;

        return $this;
    }


    /**
     * Is this content user?
     *
     * @return bool
     */
    public function isContentUser()
    {
        return $this->hasRole(self::ROLE_CONTENT_USER);
    }

    /**
     * Is this factory admin user?
     *
     * @return bool
     */
    public function isFactoryAdmin()
    {
        return $this->hasRole(self::ROLE_FACTORY_ADMIN);
    }

    /**
     * Is factory user ?
     * 
     * @return bool
     */
    public function isFactory()
    {
        return $this->factory ? true : false;
    }


    /**
     * Is retailer?
     *
     * @return bool
     */
    public function isRetailer()
    {
        if($this->retailerUserProfile 
                && $this->retailerUserProfile->getRetailerMode()
                && $this->retailerUserProfile->getRetailerProfile())
            return true;
        else
            return false;
    }

    /**
     * Is no retailer?
     *
     * @return bool
     */
    public function isNoRetailer()
    {
        return !$this->isRetailer();
    }   

    /**
     * Reset profile
     */
    protected function resetProfile()
    {
        $this->retailerUserProfile = null;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf(
            '%s %s',
            $this->customer->getFirstName(),
            $this->customer->getLastName()
        );
    }
}