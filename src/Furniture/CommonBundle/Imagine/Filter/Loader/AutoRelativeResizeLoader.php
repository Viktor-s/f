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
        
        //If need heighten resize
        if( $image->getSize()->getHeight() > $image->getSize()->getWidth() && isset($options['heighten']) ){
            return (new RelativeResize( 'heighten', $options['heighten']))->apply($image);
        //If need widen resize
        }elseif( isset($options['widen']) ){
            return (new RelativeResize( 'widen', $options['widen']))->apply($image);
        }
        
        throw new InvalidArgumentException('Unsupported options');
    }
    
}

