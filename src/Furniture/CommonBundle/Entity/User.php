<?php

namespace Furniture\CommonBundle\Entity;

use Sylius\Component\Core\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;

class User extends BaseUser 
{
    const ROLE_CONTENT_USER = 'ROLE_CONTENT_USER';
    const ROLE_FACTORY_ADMIN = 'ROLE_FACTORY_ADMIN';
    const ROLE_FACTORY_USER = 'ROLE_FACTORY_USER';

    /**
     * @var Collection
     */
    protected $factoryRelations;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->factoryRelations = new ArrayCollection();
    }
    
    /**
     * Is has factory relations?
     *
     * @return bool
     */
    public function hasFactoryRelations()
    {
        return (bool)!$this->factoryRelations->isEmpty();
    }
    
    /**
     * Get factory relations
     *
     * @return Collection
     */
    public function getFactoryRelations()
    {
        return $this->factoryRelations;
    }
    
    /**
     * Set factory relations
     *
     * @param Collection $factoryRelations
     *
     * @return User
     */
    public function setFactoryRelations(Collection $factoryRelations)
    {
        $this->factoryRelations = $factoryRelations;

        return $this;
    }
    
    /**
     * Is has factory relation?
     *
     * @param FactoryUserRelation $factoryRelations
     *
     * @return bool
     */
    public function hasFactoryRelation(FactoryUserRelation $factoryRelations)
    {
        return $this->factoryRelations->contains($factoryRelations);
    }
    
    /**
     * Add factory relation
     *
     * @param FactoryUserRelation $factoryRelations
     *
     * @return User
     */
    public function addFactoryRelation(FactoryUserRelation $factoryRelations)
    {
        if(!$this->hasFactoryRelation($factoryRelations)){
            $factoryRelations->setFactory($this);
            $this->factoryRelations->add($factoryRelations);
        }

        return $this;
    }
    
    /**
     * Remove factory relation
     *
     * @param FactoryUserRelation $factoryRelations
     *
     * @return User
     */
    public function removeFactoryRelation(FactoryUserRelation $factoryRelations)
    {
        if($this->hasUserRelation($factoryRelations)){
            $this->factoryRelations->removeElement($factoryRelations);
        }
        return $this;
    }
    
}