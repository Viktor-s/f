<?php

namespace Furniture\UserBundle\Killer\Storage;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

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
     * @var string
     */
    private $memcachedSessionKey;

    /**
     * Construct
     *
     * @param \Memcached $memcached           The memcached instance
     * @param int        $expirationTime      Expiration time for key in seconds. Default - 1 days
     * @param string     $memcachedSessionKey The key prefix for memcached session. The INI key: memcached.sess_prefix
     */
    public function __construct(\Memcached $memcached, $expirationTime = 86400, $memcachedSessionKey = 'memc.sess.key.')
    {
        $this->memcached = $memcached;
        $this->expirationTime = $expirationTime;
        $this->memcachedSessionKey = $memcachedSessionKey;
    }

    /**
     * {@inheritDoc}
     */
    public function addSession(User $user, $id)
    {
        $key = 'user_killer_sessions_' . $user->getId();

        $sessionIds = $this->memcached->get($key);

        if (!$sessionIds) {
            $sessionIds = [];
        }

        $sessionIds = $this->flushOldestSessions($sessionIds);
        $sessionIds[$id] = time();

        $this->memcached->set($key, $sessionIds, $this->expirationTime * 7);
    }

    /**
     * {@inheritDoc}
     */
    public function cleanup(User $user)
    {
        $key = 'user_killer_sessions_' . $user->getId();

        $sessionIds = $this->memcached->get($key);

        if (!$sessionIds) {
            return;
        }

        foreach ($sessionIds as $sessionId => $lastUsed) {
            $sessionKey = $this->memcachedSessionKey . $sessionId;
            $this->memcached->delete($sessionKey);
        }

        $this->memcached->delete($key);
    }

    /**
     * Cleanup oldest session ids
     *
     * @param array $sessionIds
     *
     * @return array
     */
    private function flushOldestSessions(array $sessionIds)
    {
        return array_filter($sessionIds, function ($lastUsed) {
            if ($lastUsed + $this->expirationTime < time()) {
                return false;
            }

            return true;
        });
    }
}
