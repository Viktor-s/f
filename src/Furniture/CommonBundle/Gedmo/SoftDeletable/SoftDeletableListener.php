<?php

namespace Furniture\CommonBundle\Gedmo\SoftDeletable;

use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\SoftDeleteable\SoftDeleteableListener as BaseSoftDeletableListener;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class SoftDeletableListener extends BaseSoftDeletableListener
{
    /**
     * @var array
     */
    private static $softDeletableDisableClasses = [
        TaxonInterface::class
    ];

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForObjectClass(ObjectManager $objectManager, $metadata)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
        parent::loadMetadataForObjectClass($objectManager, $metadata);

        $className = $metadata->name;

        foreach (self::$softDeletableDisableClasses as $disableClass) {
            if (is_a($className, $disableClass, true)) {
                static::$configurations[$this->name][$className]['softDeleteable'] = false;
            }
        }
    }
}
