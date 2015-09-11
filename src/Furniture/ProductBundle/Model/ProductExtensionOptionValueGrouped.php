<?php

namespace Furniture\ProductBundle\Model;

use Doctrine\Common\Collections\Collection;

class ProductExtensionOptionValueGrouped
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Collection
     */
    private $values;

    /**
     * Construct
     *
     * @param string     $name
     * @param Collection $values
     */
    public function __construct($name, Collection $values)
    {
        $this->name = $name;
        $this->values = $values;
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
     * Get values
     *
     * @return string
     */
    public function getValues()
    {
        return $this->values;
    }
}