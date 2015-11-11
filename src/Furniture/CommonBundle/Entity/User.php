<?php

namespace Furniture\CommonBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Component\Core\Model\User as BaseUser;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class User extends BaseUser 
{
    const ROLE_CONTENT_USER   = 'ROLE_CONTENT_USER'; // @todo: remove this role?
    const ROLE_FACTORY_ADMIN  = 'ROLE_FACTORY_ADMIN';
    const ROLE_FACTORY_USER   = 'ROLE_FACTORY_USER';
    const ROLE_PUBLIC_CONTENT = 'ROLE_PUBLIC_CONTENT';

    const RETAILER_ADMIN    = 1;
    const RETAILER_EMPLOYEE = 2;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var RetailerProfile
     */
    protected $retailerProfile;

    /**
     * @var int
     */
    private $retailerMode = self::RETAILER_EMPLOYEE;

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
     * Set retail profile
     *
     * @param RetailerProfile $retailerProfile
     *
     * @return User
     */
    public function setRetailerProfile(RetailerProfile $retailerProfile)
    {
        $this->resetProfile();
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
     * Is retailer?
     *
     * @return bool
     */
    public function isRetailer()
    {
        return $this->retailerProfile ? true : false;
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
     * Reset profile
     */
    protected function resetProfile()
    {
        $this->factory = null;
        $this->retailerProfile = null;
    }
}