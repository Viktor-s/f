<?php

namespace Furniture\FactoryBundle\Entity;

use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User factory rate
 *
 * @UniqueEntity(
 *      fields={"factory", "retailer"},
 *      message="The rate for this factory already exist.",
 *      errorPath="factory"
 * )
 */
class RetailerFactoryRate
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Factory
     *
     * @Assert\NotBlank()
     */
    private $factory;

    /**
     * @var RetailerProfile
     */
    private $retailer;

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
     * @return RetailerFactoryRate
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
     * Set retailer profile
     *
     * @param RetailerProfile $retailer
     *
     * @return RetailerFactoryRate
     */
    public function setRetailer(RetailerProfile $retailer)
    {
        $this->retailer = $retailer;

        return $this;
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
     * Set coefficient
     *
     * @param float $coefficient
     *
     * @return RetailerFactoryRate
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
     * @return RetailerFactoryRate
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
