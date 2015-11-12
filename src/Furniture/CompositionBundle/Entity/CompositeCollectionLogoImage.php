<?php
namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Core\Model\Image;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\CommonBundle\Uploadable\UploadableInterface;

class CompositeCollectionLogoImage extends Image implements UploadableInterface
{
    
    /**
     *
     * @var CompositeCollection
     */
    private $compositeCollection;
    
    /**
     * 
     * @return \Furniture\CompositionBundle\Entity\CompositeCollection
     */
    public function getCompositeCollection()
    {
        return $this->compositeCollection;
    }
    
    /**
     * 
     * @param \Furniture\CompositionBundle\Entity\CompositeCollection $compositeCollection
     * @return \Furniture\CompositionBundle\Entity\CompositeCollectionLogoImage
     */
    public function setCompositeCollection(CompositeCollection $compositeCollection)
    {
        $this->compositeCollection = $compositeCollection;
        return $this;
    }
    
}

