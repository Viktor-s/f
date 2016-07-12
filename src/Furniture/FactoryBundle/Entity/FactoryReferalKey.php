<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;

class FactoryReferalKey 
{
    
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     */
    private $factory;
    
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var string
     */
    private $key;
    
    /**
     * @var \DateTime
     */
    private $createdAt;
    
    /**
     * @var bool
     */
    private $enabled;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->key = md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true));
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
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function getFactory(){
        return $this->factory;
    }
    
    /**
     * 
     * @param Factory $factory
     * @return \Furniture\FactoryBundle\Entity\FactoryReferalKey
     */
    public function setFactory(Factory $factory){
        $this->factory = $factory;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\FactoryBundle\Entity\FactoryReferalKey
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getKey(){
        return $this->key;
    }
    
    /**
     * 
     * @param boolean $enabled
     * @return \Furniture\FactoryBundle\Entity\FactoryReferalKey
     */
    public function setEnabled($enabled){
        $this->enabled = (bool)$enabled;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getEnabled(){
        return $this->enabled;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getCreatedAt(){
        return $this->createdAt;
    }
    
}
