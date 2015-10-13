<?php

namespace Furniture\CommonBundle\Uploadable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

class UploadableSubscriber implements EventSubscriber
{
    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * Construct
     *
     * @param ImageUploaderInterface $imageUploader
     */
    public function __construct(ImageUploaderInterface $imageUploader)
    {
        $this->imageUploader = $imageUploader;
    }

    /**
     * On pre persist event
     *
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $this->upload($object);
    }

    /**
     * On pre update event
     *
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        $this->upload($object);
    }

    /**
     * Upload for entities
     *
     * @param object $entity
     */
    private function upload($entity)
    {
        if ($entity instanceof UploadableInterface) {
            $this->imageUploader->upload($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }
}
