<?php
namespace Furniture\CommonBundle\Imagine\Filter\Loader;

use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\RelativeResize;

class AutoRelativeResizeLoader implements LoaderInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function load(ImageInterface $image, array $options = array())
    {
        if( !count($options) )
            throw new InvalidArgumentException('Expected method/parameter pair, none given');
        
        if($image->getSize()->getHeight() == $image->getSize()->getWidth()){
            
            if(isset($options['widen']) && $options['heighten']){
                if((int)$options['widen'] > (int)$options['heighten']){
                    return (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
                }else{
                    return (new RelativeResize( 'widen', $options['widen']))->apply($image);
                }
            }elseif(isset($options['widen'])){
                return (new RelativeResize( 'widen', $options['widen']))->apply($image);
            }else{
                return (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
            }
            
            
        }elseif( $image->getSize()->getHeight() > $image->getSize()->getWidth() && isset($options['heighten']) ){
            
            $image = (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
        
            if( isset($options['widen']) && $image->getSize()->getWidth() > $options['widen'] ){
                $image = (new RelativeResize( 'widen', $options['widen']))->apply($image);
            }
            
            return $image;
        }elseif( isset($options['widen']) ){
            
            $image = (new RelativeResize( 'widen', $options['widen']))->apply($image);
            
            if( isset($options['heighten']) && $image->getSize()->getHeight() > $options['heighten'] ){
                $image = (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
            }
            
            return $image;
            
        }elseif( isset($options['heighten']) ){
            return (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
        }
        
        throw new InvalidArgumentException('Unsupported options');
    }
    
}

