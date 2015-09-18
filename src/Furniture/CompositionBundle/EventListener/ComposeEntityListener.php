<?php

namespace Furniture\CompositionBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\GenericEvent;

class ComposeEntityListener extends ContainerAware {
    
    protected function getImageUploader(){
        return $this->container->get('sylius.image_uploader');
    }
    
    public function pre_create(GenericEvent $e){
        $entity = $e->getSubject();
        $this->saveFactoryImages($entity);
        
    }
    
    public function pre_update(GenericEvent $e){
        $entity = $e->getSubject();
        $this->saveFactoryImages($entity);
        
    }
    
    protected function saveFactoryImages($entity){
        if( $entity->hasImages() ){
            
            $images = $entity->getImages();
            
            foreach($images as $image){
                $this->getImageUploader()->upload($image);
                
                if (null === $image->getPath()) {
                    $images->removeElement($image);
                }
                
            }
            
        }
        
    }
    
}

