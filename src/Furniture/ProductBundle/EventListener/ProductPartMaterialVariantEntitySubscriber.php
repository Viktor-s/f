<?php

namespace Furniture\ProductBundle\EventListener;

use Sylius\Component\Core\Uploader\ImageUploader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ProductPartMaterialVariantEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @var ImageUploader
     */
    private $uploader;

    /**
     * Construct
     *
     * @param ImageUploader $uploader
     */
    public function __construct(ImageUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Upload image
     *
     * @param GenericEvent $event
     */
    public function uploadImage(GenericEvent $event)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPartMaterialVariant $materialVariant */
        $materialVariant = $event->getSubject();

        if ($materialVariant->getImage()) {
            $image = $materialVariant->getImage();

            if ($image->getFile()) {
                $this->uploader->upload($image);
            } else {
                $materialVariant->setImage(null);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'furniture.product_part_material_variant.pre_create' => [
                'uploadImage'
            ],

            'furniture.product_part_material_variant.pre_update' => [
                'uploadImage'
            ]
        ];
    }
}
