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
     * Attention: This method kills the active user in system.
     */
    public function kill()
    {
        $request = $this->requestStack->getMasterRequest();

        if (!$request) {
            // Not found master request. Nothing action
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            // Not found token for active user. Nothing action
            return;
        }

        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return;
        }

        // Clear session
        $session = $request->getSession();
        $session->invalidate();

        // Clear security token
        $this->tokenStorage->setToken(null);

        // Mark as killer
        $this->storage->successfullyKilled($user);
    }

    /**
     * Should kill
     *
     * @param User $user
     */
    public function shouldKill(User $user)
    {
        $this->storage->shouldKill($user);
    }

    /**
     * Is should kill active user
     *
     * @return bool
     */
    public function isShouldKill()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return false;
        }

        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return false;
        }

        return $this->storage->isShouldKill($user);
    }
}
