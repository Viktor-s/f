<?php

namespace Furniture\ProductBundle\Validator\Constraint;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ProductSchemesUnique extends Constraint
{
    /** @var string */
    public $message = 'These fields must have different product schemes variants. Please create different schemes variants.';
    /** @var string */
    public $invalidMessage = 'Invalid product scheme';
}
