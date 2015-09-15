<?php

namespace Furniture\ProductBundle\Model;

use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\ProductExtensionOption;
use Furniture\ProductBundle\Entity\ProductExtensionOptionValue;

class ProductExtensionOptionValueGrouped
{
    /**
     * @var ProductExtensionOption
     */
    private $option;

    /**
     * @var Collection
     */
    private $values;

    /**
     * Construct
     *
     * @param ProductExtensionOption                   $option
     * @param Collection|ProductExtensionOptionValue[] $values
     */
    public function __construct(ProductExtensionOption $option, Collection $values)
    {
        $this->option = $option;
        $this->values = $values;
    }

    /**
     * Get option
     *
     * @return ProductExtensionOption
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->option->getName();
    }

    /**
     * Get values
     *
     * @return Collection|ProductExtensionOptionValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Get values as string
     *
     * @return string
     */
    public function getValuesAsString()
    {
        $strings = [];

        $this->values->forAll(function (ProductExtensionOptionValue $value) use (&$strings) {
            $strings[] = $value->getValue();
        });

        return implode(', ', $strings);
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValuesAsString() ?: '';
    }
}
