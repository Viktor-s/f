<?php

namespace Furniture\ProductBundle\Pattern;

use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Service for validate product variant parameters for pattern
 */
class ProductVariantParametersValidator
{
    /**
     * Validate product variant parameters
     *
     * @param ProductVariantsPattern   $pattern
     * @param ProductVariantParameters $parameters
     *
     * @return ConstraintViolationList
     */
    public function validateByPattern(ProductVariantsPattern $pattern, ProductVariantParameters $parameters)
    {
        $violations = new ConstraintViolationList();

        return $violations;
    }
}
