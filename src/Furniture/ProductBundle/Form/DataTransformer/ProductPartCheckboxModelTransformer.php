<?php

namespace Furniture\ProductBundle\Form\DataTransformer;

use Furniture\ProductBundle\Entity\ProductPart;
use Symfony\Component\Form\DataTransformerInterface;

class ProductPartCheckboxModelTransformer implements DataTransformerInterface
{
    /**
     * @var ProductPart
     */
    private $part;

    /**
     * Constructor
     *
     * @param ProductPart $part
     */
    public function __construct(ProductPart $part)
    {
        $this->part = $part;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return $this->part;
        }

        return null;
    }
}
