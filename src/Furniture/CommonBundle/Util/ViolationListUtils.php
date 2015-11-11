<?php

namespace Furniture\CommonBundle\Util;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ViolationListUtils
{
    /**
     * Convert violation list to array
     *
     * @param ConstraintViolationListInterface $violationList
     *
     * @return array
     */
    public static function convertToArrayWithoutPath(ConstraintViolationListInterface $violationList)
    {
        $errors = [];

        /** @var \Symfony\Component\Validator\ConstraintViolation $violation */
        foreach ($violationList as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}
