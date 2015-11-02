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
     * @var string
     */
    private $note;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     * @Assert\Range(min=1)
     */
    private $quantity;

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
     * @var SkuSpecificationItem
     */
    private $skuItem;
    
    /**
     * @var CustomSpecificationItem
     */
    private $customItem;

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
     * Set description
     *
     * @param string $note
     *
     * @return SpecificationItem
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return SpecificationItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
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
     * Get total price
     *
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->getPrice() * $this->quantity;
    }
    
    /**
     * Get item price
     * 
     * @return int
     */
    public function getPrice()
    {
        if ($this->getSkuItem()) {
            return $this->getSkuItem()->getProductVariant()->getPrice();
        }
        
        return 0;
    }

    /**
     * Life hook on update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
    
    /**
     * Set SKU item
     *
     * @param SkuSpecificationItem $skuItem
     *
     * @return SpecificationItem
     */
    public function setSkuItem(SkuSpecificationItem $skuItem)
    {
        $this->clearMappingFields();
        $this->skuItem = $skuItem;
        $this->skuItem->setSpecificationItem($this);

        return $this;
    }
    
    /**
     * Get SKU item
     *
     * @return SkuSpecificationItem
     */
    public function getSkuItem()
    {
        return $this->skuItem;
    }
    
    /**
     * Set custom item
     *
     * @param CustomSpecificationItem $customItem
     *
     * @return SpecificationItem
     */
    public function setCustomItem(CustomSpecificationItem $customItem)
    {
        $this->clearMappingFields();
        $this->customItem = $customItem;
        $customItem->setSpecificationItem($this);

        return $this;
    }

    /**
     * Get custom item
     *
     * @return CustomSpecificationItem
     */
    public function getCustomItem()
    {
        return $this->customItem;
    }

    /**
     * Clear mapping fields
     */
    private function clearMappingFields()
    {
        $this->skuItem = null;
    }
}
