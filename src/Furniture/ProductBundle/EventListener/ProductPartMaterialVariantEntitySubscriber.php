<?php

namespace Furniture\ProductBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param ImageUploader $uploader
     */
    public function __construct(ImageUploader $uploader, EntityManagerInterface $em)
    {
        $this->uploader = $uploader;
        $this->em = $em;
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
                // Should upload a new file
                $this->uploader->upload($image);
            } else if (!$image->getPath()) {
                // Should remove file
                $materialVariant->removeImage();

                // So, we have a association: Variant -> Image, and we set the orpharRemoval to true,
                // but doctrine2 not working with this option, and not remove image.
                // As solution we force remove image.
                $this->em->remove($image);
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
