<?php

namespace Furniture\CommonBundle\Gedmo\Loggable;

use Doctrine\Common\EventArgs;
use Gedmo\Loggable\LoggableListener as GedmoLoggableListener;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

class LoggableListener extends GedmoLoggableListener
{
    const ACTION_RESTORE = 'restore';
    /**
     * {@inheritDoc}
     */
    public function onFlush(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        /** @var \Doctrine\ORM\EntityManager $om */
        $om = $ea->getObjectManager();
        $uow = $om->getUnitOfWork();

        foreach ($ea->getScheduledObjectInsertions($uow) as $object) {
            $this->createLogEntry(self::ACTION_CREATE, $object, $ea);
        }

        foreach ($ea->getScheduledObjectUpdates($uow) as $object) {
            if ($object instanceof SoftDeletableInterface) {
                // Check for restore action.
                $changes = $uow->getEntityChangeSet($object);
                if (isset($changes['deletedAt'])) {
                    list (, $newValue) = $changes['deletedAt'];

                    if (!$newValue) {
                        // The entity restores
                        $this->createLogEntry(self::ACTION_RESTORE, $object, $ea);
                        continue;
                    }
                }

            }

            $this->createLogEntry(self::ACTION_UPDATE, $object, $ea);
        }

        foreach ($ea->getScheduledObjectDeletions($uow) as $object) {
            $this->createLogEntry(self::ACTION_REMOVE, $object, $ea);
        }
    }
}
