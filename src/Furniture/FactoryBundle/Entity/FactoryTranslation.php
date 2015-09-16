<?php

namespace Furniture\FactoryBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;

class FactoryTranslation extends AbstractTranslation
{
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $name;
    
    /**
     *
     * @var string
     */
    protected $description;
    
    /**
     * 
     * @return integer
     */
    public function getId(){
        return $this->id;
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
     * @return \Furniture\FactoryBundle\Entity\FactoryTranslation
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }
    
    /**
     * 
     * @param string $description
     * @return \Furniture\FactoryBundle\Entity\FactoryTranslation
     */
    public function setDescription($description){
        $this->description = $description;
        return $this;
    }
    
}