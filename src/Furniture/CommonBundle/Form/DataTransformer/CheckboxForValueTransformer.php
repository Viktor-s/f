<?php

namespace Furniture\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CheckboxForValueTransformer implements DataTransformerInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Construct
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        return $value ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if ($value) {
            return $this->value;
        }

        return null;
    }
}
