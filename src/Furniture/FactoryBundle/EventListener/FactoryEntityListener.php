<?php

namespace Furniture\FactoryBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\GenericEvent;

class FactoryEntityListener extends ContainerAware {
    
    protected function getImageUploader(){
        return $this->container->get('sylius.image_uploader');
    }
    
    public function pre_create(GenericEvent $e){
        $factory = $e->getSubject();
        
        $this->saveFactoryImages($factory);
        
    }
    
    public function pre_update(GenericEvent $e){
        $factory = $e->getSubject();
        
        $this->saveFactoryImages($factory);
        
    }
    
    protected function saveFactoryImages($factory){
        
        if( $factory->hasImages() ){
            
            $images = $factory->getImages();
            
            foreach($images as $image){
                $this->getImageUploader()->upload($image);
                
                if (null === $image->getPath()) {
                    $images->removeElement($image);
                }
                
            }
            
        }
        
        if( $logo = $factory->getLogoImage() )
        {
            $this->getImageUploader()->upload($logo);
            if( $logo->getPath()  === null)
            {
                
            }
        }
        
    }
    
}

