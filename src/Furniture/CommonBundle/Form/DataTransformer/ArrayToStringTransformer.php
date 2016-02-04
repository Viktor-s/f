<?php

namespace Furniture\CommonBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * Should trim delimiter in reverse transform?
     *
     * @var bool
     */
    private $trimDelimiter;

    /**
     * Construct
     *
     * @param string $delimiter
     * @param bool   $trimDelimiter
     */
    public function __construct($delimiter, $trimDelimiter = true)
    {
        $this->delimiter = $delimiter;
        $this->trimDelimiter = $trimDelimiter;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (!$value) {
            return '';
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(sprintf(
                'Can not transform value. Invalid type "%s". Should be a array.',
                gettype($value)
            ));
        }

        return implode($this->delimiter, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return [];
        }

        $delimiter = $this->delimiter;

        if ($this->trimDelimiter) {
            $delimiter = trim($delimiter);
        }

        $parts = explode($delimiter, $value);
        $parts = array_map('trim', $parts);
        $parts = array_filter($parts);

        return $parts;
    }
}
