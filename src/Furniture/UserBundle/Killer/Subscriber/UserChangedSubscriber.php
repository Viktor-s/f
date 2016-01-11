<?php

namespace Furniture\UserBundle\Killer\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\UserBundle\Entity\User;
use Furniture\UserBundle\Killer\Killer;

class UserChangedSubscriber implements EventSubscriber
{
    /**
     * @var Killer
     */
    private $killer;

    /**
     * Construct
     *
     * @param Killer $killer
     */
    public function __construct(Killer $killer)
    {
        $this->killer = $killer;
    }

    /**
     * Control user updates
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $kills =[];

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            // Control user
            if ($entity instanceof User && $entity->isShouldControlForKill()) {
                $changes = $uow->getEntityChangeSet($entity);
                $shouldKill = false;

                $fieldsForKill = [
                    'password',
                    'username',
                    'enabled'
                ];

                foreach ($fieldsForKill as $fieldName) {
                    if (isset($changes[$fieldName])) {
                        $shouldKill = true;

                        break;
                    }
                }

                if ($shouldKill) {
                    $kills[$entity->getId()] = $entity;

                    break;
                }
            }

            // Control retailer user profile
            if ($entity instanceof RetailerUserProfile) {
                $changes = $uow->getEntityChangeSet($entity);
                $shouldKill = false;

                $fieldsForKill = [
                    'retailerMode'
                ];

                foreach ($fieldsForKill as $fieldName) {
                    if (isset($changes[$fieldName])) {
                        $shouldKill = true;

                        break;
                    }
                }

                if ($shouldKill) {
                    $kills[$entity->getUser()->getId()] = $entity->getUser();
                }
            }
        }

        if (count($kills)) {
            foreach ($kills as $user) {
                $this->killer->shouldKill($user);
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
