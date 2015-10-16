<?php

namespace Furniture\FactoryBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;

class FactoryTranslation extends AbstractTranslation
{
    /**
     * @var integer
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $description;
    
    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * @var string
     */
    protected $address;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set name
     *
     * @param string $name
     *
     * @return FactoryTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set description
     *
     * @param string $description
     *
     * @return FactoryTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
    
    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }
    
    /**
     * Set short description
     *
     * @param string $description
     *
     * @return FactoryTranslation
     */
    public function setShortDescription($description)
    {
        $this->shortDescription = $description;

        return $this;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return FactoryTranslation
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }
}
