<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\UserBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;

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
     * @var \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    private $retailer;

    /**
     * @var Buyer[]
     */
    private $buyers = [];

    /**
     * Search for user
     *
     * @param User $user
     *
     * @return SpecificationQuery
     */
    public function withUser(User $user)
    {
        $this->users[spl_object_hash($user)] = $user;

        return $this;
    }

    /**
     * Search for users
     *
     * @param \Furniture\UserBundle\Entity\User[] $users
     *
     * @return SpecificationQuery
     */
    public function withUsers(array $users)
    {
        $this->users = [];

        foreach ($users as $user) {
            $this->withUser($user);
        }

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
        return array_values($this->users);
    }

    /**
     * Search with retailer.
     * Attention: clear all users, and sets users from retailer profile
     *
     * @param RetailerProfile $retailer
     *
     * @return SpecificationQuery
     */
    public function withRetailer(RetailerProfile $retailer)
    {
        $this->retailer = $retailer;
        
        return $this;
    }

    /**
     * Has retailer?
     *
     * @return bool
     */
    public function hasRetailer()
    {
        return (bool)$this->retailer;
    }

    /**
     * Get retailer
     *
     * @return RetailerProfile
     */
    public function getRetailer()
    {
        return $this->retailer;
    }

    /**
     * With buyer
     *
     * @param Buyer $buyer
     *
     * @return SpecificationQuery
     */
    public function withBuyer(Buyer $buyer)
    {
        $this->buyers[$buyer->getId()] = $buyer;

        return $this;
    }

    /**
     * With buyers
     *
     * @param Buyer[] $buyers
     *
     * @return SpecificationQuery
     */
    public function withBuyers(array $buyers)
    {
        $this->buyers = [];

        foreach ($buyers as $buyer) {
            $this->withBuyer($buyer);
        }

        return $this;
    }

    /**
     * Has buyers?
     *
     * @return bool
     */
    public function hasBuyers()
    {
        return count($this->buyers) > 0;
    }

    /**
     * Get buyers
     *
     * @return Buyer[]
     */
    public function getBuyers()
    {
        return array_values($this->buyers);
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
     * Only finished
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
