<?php
namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Core\Model\Image;
use Furniture\CommonBundle\Uploadable\UploadableInterface;

class CompositeCollectionLogoImage extends Image implements UploadableInterface
{
    /**
     * @var CompositeCollection
     */
    private $compositeCollection;

    /**
     * Get composite collection
     *
     * @return CompositeCollection
     */
    public function getCompositeCollection()
    {
        return $this->compositeCollection;
    }

    /**
     * Set composite collection
     *
     * @param CompositeCollection $compositeCollection
     *
     * @return CompositeCollectionLogoImage
     */
    public function setCompositeCollection(CompositeCollection $compositeCollection)
    {
        $this->compositeCollection = $compositeCollection;

        return $this;
    }
}
