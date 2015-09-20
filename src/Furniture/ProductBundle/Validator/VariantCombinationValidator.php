<?php

namespace Furniture\ProductBundle\Validator;

use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class VariantCombinationValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, VariantInterface::class);
        }
    }
}
