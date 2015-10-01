<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;

class ProductPartTranslation extends AbstractTranslation 
{
    
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

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
     * 
     * @param string $label
     * @return \Furniture\ProductBundle\Entity\ProductPartTranslation
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    
}

