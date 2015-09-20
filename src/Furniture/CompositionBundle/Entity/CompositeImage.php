<?php

namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Core\Model\Image;

class CompositeImage extends Image 
{
    /**
     *
     * @var \Furniture\CompositionBundle\Entity\Composite
     */
    protected $composite;
    
    /**
     * 
     * @return \Furniture\CompositionBundle\Entity\Composite
     */
    public function getComposite(){
        return $this->composite;
    }
    
    /**
     * 
     * @param \Furniture\CompositionBundle\Entity\Composite $factory
     * @return \Furniture\CompositionBundle\Entity\CompositeImage
     */
    public function setComposite(\Furniture\CompositionBundle\Entity\Composite $factory){
        $this->composite = $factory;
        return $this;
    }
    
}