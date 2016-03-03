<?php

namespace Furniture\ProductBundle\Validator\Constraint;

use Furniture\ProductBundle\Entity\ProductScheme;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProductSchemesUniqueValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ProductSchemesUnique) {
            throw new UnexpectedTypeException($constraint, ProductSchemesUnique::class);
        }

        if (!$value instanceof \Traversable && !is_array($value)) {
            throw new UnexpectedTypeException($value, \Traversable::class);
        }

        $hashes = [];

        foreach ($value as $index => $productScheme) {
            if (!$productScheme instanceof ProductScheme) {
                $this->buildViolation($constraint->invalidMessage)
                    ->atPath($index)
                    ->addViolation();

                continue;
            }

            $schemeParts = [];

            foreach ($productScheme->getProductParts() as $part) {
                $schemeParts[] = $part->getId();
            }

            sort($schemeParts);
            $hash = implode($schemeParts);

            if (in_array($hash, $hashes)) {
                $this->buildViolation($constraint->message)
                    ->atPath($index)
                    ->addViolation();
            }

            $hashes[] = $hash;
        }
    }
}
