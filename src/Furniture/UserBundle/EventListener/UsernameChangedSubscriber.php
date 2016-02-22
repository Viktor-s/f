<?php

namespace Furniture\UserBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\UserBundle\Entity\Customer;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UsernameChangedSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
                    $this->changeUsernameForUser($entity);

                    $uow->recomputeSingleEntityChangeSet($userClassMetadata, $entity);
                }
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Customer) {
                /** @var User $user */
                $user = $entity->getUser();

                if (null !== $user && $user->getUsername() !== $entity->getEmail()) {
                    $user->setUsername($entity->getEmail());
                    $this->changeUsernameForUser($user);

                    $uow->recomputeSingleEntityChangeSet($customerClassMetadata, $user);
                }
            } else if ($entity instanceof User) {
                $customer = $entity->getCustomer();

                if ($entity->getUsername() !== $customer->getEmail()) {
                    $entity->setUsername($customer->getEmail());
                    $this->changeUsernameForUser($entity);
                    $uow->recomputeSingleEntityChangeSet($userClassMetadata, $entity);
                }
            }
        }
    }

    /**
     * Change username/email for user
     *
     * @param User $user
     */
    private function changeUsernameForUser(User $user)
    {
        if ($user->getVerifyEmailHash()) {
            $this->container->get('user.email_verifier')->verifyEmail($user);
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
