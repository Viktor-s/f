<?php

namespace Furniture\ProductBundle\Pattern\Exception;

class ManyProductVariantsByParametersException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = 'Many product variants by parameters.', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
