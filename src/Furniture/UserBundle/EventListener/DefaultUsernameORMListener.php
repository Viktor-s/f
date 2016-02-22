<?php

namespace Furniture\UserBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\UserBundle\Entity\Customer;
use Furniture\UserBundle\Entity\User;

/**
 * We should override the \Sylius\Bundle\UserBundle\EventListener\DefaultUsernameORMListener, because
 * the not working change any field in preUpdate event.
 * For more information please see: https://github.com/Sylius/Sylius/issues/3939
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class DefaultUsernameORMListener implements EventSubscriber
{
    /**
     * On flush
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }
}
