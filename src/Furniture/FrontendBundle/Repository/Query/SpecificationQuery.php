<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\CommonBundle\Entity\User;

class SpecificationQuery
{
    const STATE_OPENED   = 1;
    const STATE_FINISHED = 2;

    /**
     * @var int
     */
    private $state;

    /**
     * @var User[]
     */
    private $users = [];

    /**
     * Search for user
     *
     * @param User $user
     *
     * @return SpecificationQuery
     */
    public function forUser(User $user)
    {
        $this->users[spl_object_hash($user)] = $user;

        return $this;
    }

    /**
     * Has users
     *
     * @return bool
     */
    public function hasUsers()
    {
        return count($this->users) > 0;
    }

    /**
     * Get users
     *
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Only opened
     *
     * @return SpecificationQuery
     */
    public function opened()
    {
        $this->state = self::STATE_OPENED;

        return $this;
    }

    /**
     * Is search only opened specifications
     *
     * @return bool
     */
    public function isOpened()
    {
        return $this->state == self::STATE_OPENED;
    }

    /**
     * Only finisher
     *
     * @return SpecificationQuery
     */
    public function finished()
    {
        $this->state = self::STATE_FINISHED;

        return $this;
    }

    /**
     * Is search only finished specifications
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->state == self::STATE_FINISHED;
    }

    /**
     * Has state
     *
     * @return bool
     */
    public function hasState()
    {
        return $this->state !== null;
    }
}
