<?php

namespace Furniture\ProductBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ProductVariant extends Constraint
{
    
    public $message = 'invalid product variant combination!';
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
    
}