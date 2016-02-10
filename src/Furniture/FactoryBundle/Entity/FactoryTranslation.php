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
     * @var string
     */
    private $collectionContent;

    /**
     * @var string
     */
    private $workInfoContent;

    /**
     * @var string
     */
    private $bankDetails;

    /**
     * @var string
     */
    private $productTime;

    /**
     * @var string
     */
    private $deliveryAndPackaging;

    /**
     * @var string
     */
    private $vacations;
    
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

    /**
     * Set collection content
     *
     * @param string $collectionContent
     *
     * @return FactoryTranslation
     */
    public function setCollectionContent($collectionContent)
    {
        $this->collectionContent = $collectionContent;

        return $this;
    }

    /**
     * Get collection content
     *
     * @return string
     */
    public function getCollectionContent()
    {
        return $this->collectionContent;
    }

    /**
     * Set work info content
     *
     * @param string $workInfoContent
     *
     * @return FactoryTranslation
     */
    public function setWorkInfoContent($workInfoContent)
    {
        $this->workInfoContent = $workInfoContent;

        return $this;
    }

    /**
     * Get work info content
     *
     * @return string
     */
    public function getWorkInfoContent()
    {
        return $this->workInfoContent;
    }

    /**
     * Get Bank details
     *
     * @return string
     */
    public function getBankDetails()
    {
        return $this->bankDetails;
    }

    /**
     * Set Bank details.
     *
     * @param $bankDetails
     * @return FactoryTranslation
     */
    public function setBankDetails($bankDetails)
    {
        $this->bankDetails = $bankDetails;

        return $this;
    }

    /**
     * Get production time
     *
     * @return string
     */
    public function getProductTime()
    {
        return $this->productTime;
    }

    /**
     * Set production time.
     *
     * @param $productTime
     * @return FactoryTranslation
     */
    public function setProductTime($productTime)
    {
        $this->productTime = $productTime;

        return $this;
    }

    /**
     * Get delivery and apcking info.
     *
     * @return string
     */
    public function getDeliveryAndPackaging()
    {
        return $this->deliveryAndPackaging;
    }

    /**
     * @param $deliveryAndPackging
     * @return FactoryTranslation
     */
    public function setDeliveryAndPackaging($deliveryAndPackging)
    {
        $this->deliveryAndPackaging = $deliveryAndPackging;

        return $this;
    }

    /**
     * Get vacations.
     *
     * @return string
     */
    public function getVacations()
    {
        return $this->vacations;
    }

    /**
     * Set vacations info.
     *
     * @param $vacations
     * @return $this
     */
    public function setVacations($vacations)
    {
        $this->vacations = $vacations;

        return $this;
    }
}
