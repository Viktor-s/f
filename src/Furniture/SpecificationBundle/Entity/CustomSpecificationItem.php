<?php

namespace Furniture\SpecificationBundle\Entity;

class CustomSpecificationItem 
{
    
    /**
     *
     * @var int
     */
    private $id;
    
    /**
     *
     * @var \Furniture\SpecificationBundle\Entity\SpecificationItem
     */
    private $specificationItem;
    
    /**
     *
     * @var \Furniture\SpecificationBundle\Entity\CustomSpecificationItemImage
     */
    private $image;
    
    /**
     *
     * @var string
     */
    private $factoryName;
    
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var int
     */
    private $price;
            
    function __construct() {
        ;
    }
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @param \Furniture\SpecificationBundle\Entity\SpecificationItem $specificationItem
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function setSpecificationItem(SpecificationItem $specificationItem)
    {
        $this->specificationItem = $specificationItem;
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\SpecificationBundle\Entity\SpecificationItem
     */
    public function getSpecificationItem()
    {
        return $this->specificationItem;
    }
    
    /**
     * 
     * @param \Furniture\SpecificationBundle\Entity\CustomSpecificationItemImage $image
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function setImage(CustomSpecificationItemImage $image)
    {
        $this->image = $image;
        $this->image->setCustomSpecificationItem($this);
        return $this;
    }
    
    /**
     * 
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItemImage
     */
    public function getImage()
    {
        return $this->image;
    }
 
    /**
     * 
     * @param str $name
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function setFactoryName($name)
    {
        $this->factoryName = $name;
        return $this;
    }
    
    /**
     * 
     * @return str
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }
    
    /**
     * 
     * @param str $name
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * 
     * @return str
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @param int $price
     * @return \Furniture\SpecificationBundle\Entity\CustomSpecificationItem
     */
    public function setPrice($price)
    {
        $this->price = (int)$price;
        return $this;
    }
    
    /**
     * 
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
    
}
