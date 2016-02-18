<?php

namespace Furniture\ProductBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Product\Model\AttributeValue;

class ProductAttributeValueMetadataSubscriber implements EventSubscriber
{
    /**
     * Load class metadata
     *
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $classMetadata */
        $classMetadata = $event->getClassMetadata();

        if ($classMetadata->getName() === AttributeValue::class) {
            foreach ($classMetadata->associationMappings as $key => $associationMapping) {
                if ($associationMapping['fieldName'] == 'attribute') {
                    $classMetadata->associationMappings[$key]['joinColumns'][0]['onDelete'] = 'RESTRICT';
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }
}
