<?php

namespace Furniture\UserBundle\Killer\Storage;

use Furniture\UserBundle\Entity\User;

interface StorageInterface
{
    /**
     * Save session identifier for user
     *
     * @param User   $user
     * @param string $id
     */
    public function addSession(User $user, $id);

    /**
     * Cleanup sessions for user
     *
     * @param User $user
     */
    public function cleanup(User $user);
}
