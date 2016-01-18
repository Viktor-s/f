<?php

namespace Furniture\RetailerBundle\Entity;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     *
     * @Assert\NotBlank(groups={"RetailerCreate", "RetailerUpdate"})
     */
    protected $retailerProfile;
    
    /**
     * @var int
     *
     * @Assert\Choice(groups={"Default", "Create", "Update"}, choices={1, 2})
     * @Assert\NotBlank(groups={"RetailerCreate", "RetailerUpdate"})
     */
    private $retailerMode;
    
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
     * @return \Furniture\UserBundle\Entity\User
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
     * @return \Furniture\UserBundle\Entity\User
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

    /**
     * Check requirements
     *
     * @Assert\Callback(groups={"Default", "Create", "Update"})
     *
     * @param ExecutionContextInterface $context
     */
    public function checkRequirements(ExecutionContextInterface $context)
    {
        if ($this->retailerProfile && !$this->retailerMode) {
            $context->buildViolation('This value should be not blank.')
                ->atPath('retailerMode')
                ->addViolation();
        }

        if ($this->retailerMode && !$this->retailerProfile) {
            $context->buildViolation('This value should be not blank.')
                ->atPath('retailerProfile')
                ->addViolation();
        }
    }
}
