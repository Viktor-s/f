<?php

namespace Furniture\UserBundle\Killer\Storage;

use Furniture\UserBundle\Entity\User;

class MemcachedStorage implements StorageInterface
{
    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * @var integer
     */
    private $expirationTime;

    /**
     * Construct
     *
     * @param \Memcached $memcached      The memcached instance
     * @param int        $expirationTime Expiration time for key in seconds. Default - 7 days
     */
    public function __construct(\Memcached $memcached, $expirationTime = 604800)
    {
        $this->memcached = $memcached;
        $this->expirationTime = $expirationTime;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldKill(User $user)
    {
        $key = $this->generateKey($user);
        $this->memcached->set($key, true, $this->expirationTime);
    }

    /**
     * {@inheritDoc}
     */
    public function isShouldKill(User $user)
    {
        $key = $this->generateKey($user);

        return (bool) $this->memcached->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function successfullyKilled(User $user)
    {
        $key = $this->generateKey($user);
        $this->memcached->delete($key);
    }

    /**
     * Generate key for user
     *
     * @param User $user
     *
     * @return string
     */
    private function generateKey(User $user)
    {
        return 'should_kill_' . $user->getId();
    }
}
