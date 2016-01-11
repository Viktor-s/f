<?php

namespace Furniture\UserBundle\Killer\Storage;

use Furniture\UserBundle\Entity\User;

interface StorageInterface
{
    /**
     * Should kill user from session on next login
     *
     * @param User $user
     */
    public function shouldKill(User $user);

    /**
     * Is should user killed
     *
     * @param User $user
     *
     * @return bool
     */
    public function isShouldKill(User $user);

    /**
     * Mark user as successfully killed from system
     *
     * @param User $user
     */
    public function successfullyKilled(User $user);
}
