<?php

namespace Furniture\UserBundle\Killer\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\UserBundle\Entity\User;
use Furniture\UserBundle\Killer\Killer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserChangedSubscriber implements EventSubscriber
{
    /**
     * @var Killer
     */
    private $killer;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param Killer                $killer
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Killer $killer, TokenStorageInterface $tokenStorage)
    {
        $this->killer = $killer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Control user updates
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $token = $this->tokenStorage->getToken();

        $activeUser = null;

        if ($token) {
            $user = $token->getUser();

            if ($user && $user instanceof User) {
                $activeUser = $user;
            }
        }

        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        /** @var User[] $kills */
        $kills = [];

        $isAnyFieldChanged = function ($entity, array $fields) use ($uow)
        {
            $changes = $uow->getEntityChangeSet($entity);

            foreach ($fields as $fieldName) {
                if (isset($changes[$fieldName])) {
                    return true;
                }
            }

            return false;
        };

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            // Control user
            if ($entity instanceof User) {
                $fields = [
                    'enabled',
                    'password',
                    'username',
                    'needResetPassword'
                ];

                if ($isAnyFieldChanged($entity, $fields)) {
                    $kills[$entity->getId()] = $entity;
                }
            }

            // Control retailer user profile
            if ($entity instanceof RetailerUserProfile) {
                $fields = [
                    'retailerMode',
                ];

                if ($isAnyFieldChanged($entity, $fields)) {
                    $kills[$entity->getUser()->getId()] = $entity->getUser();
                }
            }
        }

        if (count($kills)) {
            foreach ($kills as $user) {
                if ($activeUser && $activeUser->getId() != $user->getId()) {
                    // Kill only non active user.
                    $this->killer->kill($user);
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }
}
