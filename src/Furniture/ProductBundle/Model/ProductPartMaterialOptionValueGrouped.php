<?php

namespace Furniture\ProductBundle\Model;

use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\ProductPartMaterialOption;
use Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;

class ProductPartMaterialOptionValueGrouped
{
    /**
     * @var ProductPartMaterialOption
     */
    private $option;

    /**
     * @var Collection
     */
    private $values;

    /**
     * Construct
     *
     * @param ProductPartMaterialOption                   $option
     * @param Collection|ProductPartMaterialOptionValue[] $values
     */
    public function __construct(ProductPartMaterialOption $option, Collection $values)
    {
        $this->option = $option;
        $this->values = $values;
    }

    /**
     * Get option
     *
     * @return ProductPartMaterialOption
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
     * @return Collection|ProductPartMaterialOptionValue[]
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

        $this->values->forAll(function (ProductPartMaterialOptionValue $value) use (&$strings) {
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
