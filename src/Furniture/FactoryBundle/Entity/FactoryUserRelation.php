<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\CommonBundle\Entity\User;

class FactoryUserRelation
{
    
    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var \Furniture\FactoryBundle\Entity\Factory
     */
    protected $factory;
    
    /**
     *
     * @var \Furniture\CommonBundle\Entity\User
     */
    protected $user;
    
    /**
     *
     * @var bool
     */
    protected $accessProducts;
    
    /**
     *
     * @var bool
     */
    protected $accessProductsPrices;
    
    /**
     *
     * @var int
     */
    protected $discount;
    
    /**
     *
     * @var bool
     */
    protected $isActive;

    /**
     * 
     * @return bool
     */
    public function getIsActive(){
        return $this->isActive;
    }

    /**
     * 
     * @param bool $status
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setIsActive($status){
        $this->isActive = (bool)$status;
        return $this;
    }

        /**
     * 
     * @return int
     */
    public function getId(){
        return $this->id;
    }


    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\Factory $factory
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setFactory(Factory $factory){
        $this->factory = $factory;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function getFactory(){
        return $this->factory;
    }
    
    /**
     * 
     * @param \Furniture\CommonBundle\Entity\User $user
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setUser(User $user){
        $this->user = $user;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\CommonBundle\Entity\User
     */
    public function getUser(){
        return $this->user;
    }
    
    /**
     * 
     * @param bool $status
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setAccessProducts($status){
        $this->accessProducts = (bool)$status;
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function getAccessProducts(){
        return $this->accessProducts;
    }
    
    /**
     * 
     * @param bool $status
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setAccessProductsPrices($status){
        $this->accessProductsPrices = (bool)$status;
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function getAccessProductsPrices(){
        return $this->accessProductsPrices;
    }
    
    /**
     * 
     * @param int $percent
     * @return \Furniture\FactoryBundle\Entity\FactoryUserRelation
     */
    public function setDiscount($percent){
        $this->discount = (int)$percent;
        return $this;
    }
    
    /**
     * 
     * @return int
     */
    public function getDiscount(){
        return $this->discount;
    }
    
}