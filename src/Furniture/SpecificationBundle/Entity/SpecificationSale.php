<?php

namespace Furniture\SpecificationBundle\Entity;

class SpecificationSale
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Specification
     */
    private $specification;

    /**
     * In percentage
     *
     * @var float
     *
     * @Assert\Range(min = 0.01, max = 100)
     */
    private $sale;

    /**
     * @var int
     */
    private $position = 0;

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
     * Set specification
     *
     * @param Specification $specification
     *
     * @return SpecificationSale
     */
    public function setSpecification(Specification $specification)
    {
        $this->specification = $specification;

        return $this;
    }

    /**
     * Get specification
     *
     * @return Specification
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * Set sale
     *
     * @param float $sale
     *
     * @return SpecificationSale
     */
    public function setSale($sale)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return float
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return SpecificationSale
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
