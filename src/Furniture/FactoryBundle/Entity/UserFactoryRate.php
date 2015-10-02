<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User factory rate
 *
 * @UniqueEntity(
 *      fields={"factory", "user"},
 *      message="The rate for this factory already exist.",
 *      errorPath="factory"
 * )
 */
class UserFactoryRate
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var User
     */
    private $user;

    /**
     * @var float
     *
     * @Assert\Range(min = 1)
     */
    private $coefficient;

    /**
     * In percentage (1 - 100)
     *
     * @var float
     *
     * @Assert\Range(min = 0, max = 100)
     */
    private $dumping;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set factory
     *
     * @param Factory $factory
     *
     * @return UserFactoryRate
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return UserFactoryRate
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set coefficient
     *
     * @param float $coefficient
     *
     * @return UserFactoryRate
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient
     *
     * @return float
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set dumping (In percentage)
     *
     * @param float $dumping
     *
     * @return UserFactoryRate
     */
    public function setDumping($dumping)
    {
        $this->dumping = $dumping;

        return $this;
    }

    /**
     * Get dumping
     *
     * @return float
     */
    public function getDumping()
    {
        return $this->dumping;
    }
}
