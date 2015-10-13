<?php

namespace Furniture\FactoryBundle\Entity;

use Sylius\Component\Core\Model\Image;

class FactoryLogoImage extends Image 
{
    /**
     *
     * @var \Furniture\FactoryBundle\Entity\Factory
     */
    protected $factory;
    
    /**
     * 
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function getFactory(){
        return $this->factory;
    }
    
    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\Factory $factory
     * @return \Furniture\FactoryBundle\Entity\FactoryImage
     */
    public function setFactory(\Furniture\FactoryBundle\Entity\Factory $factory){
        $this->factory = $factory;
        return $this;
    }
    
}