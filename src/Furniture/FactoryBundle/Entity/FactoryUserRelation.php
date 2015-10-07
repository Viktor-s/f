<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\CommonBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class FactoryUserRelation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Furniture\FactoryBundle\Entity\Factory
     */
    private $factory;
    
    /**
     * @var \Furniture\CommonBundle\Entity\User
     */
    private $user;
    
    /**
     * @var bool
     */
    private $accessProducts = false;
    
    /**
     * @var bool
     */
    private $accessProductsPrices = false;
    
    /**
     * The discount for user in percentage
     *
     * @var int
     */
    private $discount = 0;
    
    /**
     * @var bool
     */
    private $active = false;

    /**
     * @var bool
     */
    private $userAccept;

    /**
     * @var bool
     */
    private $factoryAccept;

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
     * Set factory
     *
     * @param Factory $factory
     *
     * @return FactoryUserRelation
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }
    
    /**
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }
    
    /**
     * Set user
     *
     * @param User $user
     *
     * @return FactoryUserRelation
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
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
     * Set right for access to products
     *
     * @param bool $status
     *
     * @return FactoryUserRelation
     */
    public function setAccessProducts($status)
    {
        $this->accessProducts = (bool)$status;

        return $this;
    }
    
    /**
     * Is access to products
     *
     * @return bool
     */
    public function isAccessProducts()
    {
        return $this->accessProducts;
    }
    
    /**
     * Set rights for access to product prices
     *
     * @param bool $status
     *
     * @return FactoryUserRelation
     */
    public function setAccessProductsPrices($status)
    {
        $this->accessProductsPrices = (bool)$status;

        return $this;
    }
    
    /**
     * Is rights for access to product prices
     *
     * @return bool
     */
    public function isAccessProductsPrices()
    {
        return $this->accessProductsPrices;
    }
    
    /**
     * Set discount
     *
     * @param int $percent
     *
     * @return FactoryUserRelation
     */
    public function setDiscount($percent)
    {
        $this->discount = (int)$percent;

        return $this;
    }
    
    /**
     * Get discount
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Is active relation
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set active
     *
     * @param bool $status
     *
     * @return FactoryUserRelation
     */
    public function setActive($status)
    {
        $this->active = (bool) $status;

        return $this;
    }

    /**
     * Set user accept
     *
     * @param bool $accept
     *
     * @return FactoryUserRelation
     */
    public function setUserAccept($accept)
    {
        $this->userAccept = (bool) $accept;

        return $this;
    }

    /**
     * Is user accept
     *
     * @return bool
     */
    public function isUserAccept()
    {
        return $this->userAccept;
    }

    /**
     * Set factory accept
     *
     * @param bool $accept
     *
     * @return FactoryUserRelation
     */
    public function setFactoryAccept($accept)
    {
        $this->factoryAccept = (bool) $accept;

        return $this;
    }

    /**
     * Is user accept
     *
     * @return bool
     */
    public function isFactoryAccept()
    {
        return $this->factoryAccept;
    }
}
