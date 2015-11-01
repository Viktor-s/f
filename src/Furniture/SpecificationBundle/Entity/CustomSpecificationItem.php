<?php

namespace Furniture\SpecificationBundle\Entity;

class CustomSpecificationItem 
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var SpecificationItem
     */
    private $specificationItem;
    
    /**
     * @var CustomSpecificationItemImage
     */
    private $image;
    
    /**
     * @var string
     */
    private $factoryName;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var int
     */
    private $price;
    
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
     * Set specification item
     *
     * @param SpecificationItem $specificationItem
     *
     * @return CustomSpecificationItem
     */
    public function setSpecificationItem(SpecificationItem $specificationItem)
    {
        $this->specificationItem = $specificationItem;

        return $this;
    }
    
    /**
     * Get specification item
     *
     * @return SpecificationItem
     */
    public function getSpecificationItem()
    {
        return $this->specificationItem;
    }
    
    /**
     * Set image
     *
     * @param CustomSpecificationItemImage $image
     *
     * @return CustomSpecificationItem
     */
    public function setImage(CustomSpecificationItemImage $image)
    {
        $this->image = $image;
        $this->image->setCustomSpecificationItem($this);

        return $this;
    }
    
    /**
     * Get image
     *
     * @return CustomSpecificationItemImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Remove image
     *
     * @return CustomSpecificationItemImage
     */
    public function removeImage()
    {
        $image = $this->image;
        $this->image = null;

        return $image;
    }
 
    /**
     * Set factory name
     *
     * @param string $name
     *
     * @return CustomSpecificationItem
     */
    public function setFactoryName($name)
    {
        $this->factoryName = $name;

        return $this;
    }
    
    /**
     * Get factory name
     *
     * @return string
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return CustomSpecificationItem
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set price
     *
     * @param int $price
     *
     * @return CustomSpecificationItem
     */
    public function setPrice($price)
    {
        $this->price = (int)$price;

        return $this;
    }
    
    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
}
