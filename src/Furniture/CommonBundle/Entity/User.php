<?php

namespace Furniture\CommonBundle\Entity;

use Sylius\Component\Core\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;

class User extends BaseUser 
{

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $factoryRelations;
    
    function __construct() {
        parent::__construct();
        $this->factoryRelations = new ArrayCollection();
    }
    
    /**
     * 
     * @return bool
     */
    public function hasFactoryRelations(){
        return (bool)!$this->factoryRelations->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFactoryRelations(){
        return $this->factoryRelations;
    }
    
    /**
     * 
     * @param Collection $factoryRelations
     * @return \Furniture\CommonBundle\Entity\User
     */
    public function setFactoryRelations(Collection $factoryRelations){
        $this->factoryRelations = $factoryRelations;
        return $this;
    }
    
    /**
     * 
     * @param FactoryUserRelation $factoryRelations
     * @return bool
     */
    public function hasFactoryRelation(FactoryUserRelation $factoryRelations){
        return $this->factoryRelations->contains($factoryRelations);
    }
    
    /**
     * 
     * @param FactoryUserRelation $factoryRelations
     * @return \Furniture\CommonBundle\Entity\User
     */
    public function addFactoryRelation(FactoryUserRelation $factoryRelations){
        if(!$this->hasFactoryRelation($factoryRelations)){
            $factoryRelations->setFactory($this);
            $this->factoryRelations->add($factoryRelations);
        }
        return $this;
    }
    
    /**
     * 
     * @param FactoryUserRelation $factoryRelations
     * @return \Furniture\CommonBundle\Entity\User
     */
    public function removeFactoryRelation(FactoryUserRelation $factoryRelations){
        if($this->hasUserRelation($factoryRelations)){
            $this->factoryRelations->removeElement($factoryRelations);
        }
        return $this;
    }
    
}