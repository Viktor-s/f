<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Validator\Constraints as Assert;

class SpecificationItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Specification
     *
     * @Assert\NotBlank()
     */
    private $specification;

    /**
     * @var ProductVariant
     *
     * @Assert\NotBlank()
     */
    private $productVariant;

    /**
     * @var Composite
     */
    private $composite;

    /**
     * @var int
     */
    private $cost;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * @return SpecificationItem
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
     * Set product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return SpecificationItem
     */
    public function setProductVariant(ProductVariant $productVariant)
    {
        $this->productVariant = $productVariant;

        return $this;
    }

    /**
     * Get product variant
     *
     * @return ProductVariant
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }

    /**
     * Set composition
     *
     * @param Composite $composite
     *
     * @return SpecificationItem
     */
    public function setComposition(Composite $composite)
    {
        $this->composite = $composite;

        return $this;
    }

    /**
     * Get composite
     *
     * @return Composite
     */
    public function getComposite()
    {
        return $this->composite;
    }

    /**
     * Set cost
     *
     * @param int $cost
     *
     * @return SpecificationItem
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Life hook on update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
