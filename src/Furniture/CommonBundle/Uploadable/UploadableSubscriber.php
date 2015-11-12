<?php

namespace Furniture\CommonBundle\Uploadable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
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
     * On flush
     *
     * @
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->uploadOrRemove($em, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->uploadOrRemove($em, $entity);
        }
    }

    /**
     * Upload for entities
     *
     * @param EntityManagerInterface $em
     * @param object                 $entity
     */
    private function uploadOrRemove(EntityManagerInterface $em, $entity)
    {
        if ($entity instanceof UploadableInterface) {
            $uow = $em->getUnitOfWork();
            if (!$entity->getPath() && !$entity->getFile()) {
                // Remove
                $uow->clearEntityChangeSet(spl_object_hash($entity));
                $em->remove($entity);
            } else {
                // Upload
                $this->imageUploader->upload($entity);
                $classMetadata = $em->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }
}
