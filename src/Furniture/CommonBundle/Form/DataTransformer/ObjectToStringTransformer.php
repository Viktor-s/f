<?php

namespace Furniture\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ObjectToStringTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        if (!is_object($value)) {
            throw new TransformationFailedException(sprintf(
                'Invalid value. Should be a object, but "%s" given.',
                gettype($value)
            ));
        }

        return (string) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        throw new \BadMethodCallException('You system do not call to this method. Please set "disabled" option.');
    }
}
