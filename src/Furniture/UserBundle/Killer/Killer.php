<?php

namespace Furniture\UserBundle\Killer;

use Furniture\UserBundle\Entity\User;
use Furniture\UserBundle\Killer\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Killer
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param StorageInterface      $storage
     * @param RequestStack          $requestStack
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        StorageInterface $storage,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->storage = $storage;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Kill user.
     *
     * @param User $user
     */
    public function kill(User $user)
    {
        $this->storage->cleanup($user);
    }

    /**
     * Collect session id.
     * Attention: this method save session id only for active user in system.
     *
     * @param string $id
     */
    public function collectionSessionId($id)
    {
        $user = $this->getUser();

        if ($user) {
            $this->storage->addSession($user, $id);
        }
    }

    /**
     * Get user
     *
     * @return \Furniture\UserBundle\Entity\User|null
     */
    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            // Not found token for active user. Nothing action
            return null;
        }

        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return null;
        }

        return $user;
    }
}
