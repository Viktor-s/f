<?php

namespace Furniture\ProductBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Furniture\ProductBundle\Entity\ProductVariant;

/**
 * The Doctrine ORM not supports override orderBy attribute, and
 * we create this class for override ProductVariant::images "orderBy"
 * attribute.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ProductVariantMetadataSubscriber implements EventSubscriber
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

        if ($classMetadata->getName() === ProductVariant::class) {
            $classMetadata->associationMappings['images']['orderBy'] = [
                'position' => 'ASC'
            ];
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
