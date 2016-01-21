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
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $userClassMetadata = $em->getClassMetadata(User::class);
        $customerClassMetadata = $em->getClassMetadata(Customer::class);

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $customer = $entity->getCustomer();
                if (null !== $customer && $customer->getEmail() !== $entity->getUsername()) {
                    $entity->setUsername($customer->getEmail());

                    $uow->recomputeSingleEntityChangeSet($userClassMetadata, $entity);
                }
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Customer) {
                $user = $entity->getUser();

                if (null !== $user && $user->getUsername() !== $entity->getEmail()) {
                    $user->setUsername($entity->getEmail());
                    $uow->recomputeSingleEntityChangeSet($customerClassMetadata, $user);
                }
            }
        }
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
