<?php

namespace Furniture\SkuOptionBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;

class SkuOptionTypeTranslation extends AbstractTranslation {
    
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
    
    public function __construct(){
        
    }
    
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
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionType
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }
    
}

