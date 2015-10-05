<?php

namespace Furniture\SpecificationBundle\EventListener\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;

class SpecificationModifiedSubscriber implements EventSubscriber
{
    /**
     * On flush event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $updatedSpecifications = new ArrayCollection();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledCollectionUpdates(),
            $uow->getScheduledEntityDeletions()
        );

        foreach ($entities as $entity) {
            if ($entity instanceof SpecificationItem) {
                $specification = $entity->getSpecification();

                if ($specification && !$updatedSpecifications->contains($specification)) {
                    $updatedSpecifications->add($specification);
                }
            }
        }

        $specificationMetadata = $em->getClassMetadata(Specification::class);

        $updatedSpecifications->forAll(function ($index, Specification $specification) use ($uow, $specificationMetadata) {
            $specification->onUpdate();
            $uow->computeChangeSet($specificationMetadata, $specification);

            return true;
        });
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
