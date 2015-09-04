<?php

namespace Furniture\SkuOptionBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;

class SkuOptionType extends AbstractTranslatable {
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $typeCode;
    
    public function __construct(){
        parent::__construct();
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
        return $this->translate()->getName();
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionType
     */
    public function setName($name){
        $this->translate()->setName($name);
        return $this;
    }
    
    /**
     * Return translation model class.
     *
     * @return string
     */
    public static function getTranslationClass()
    {
        return get_called_class().'Translation';
    }
    
}