<?php

namespace Furniture\SpecificationBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\Specification;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SpecificationIdModelTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (!$value) {
            return null;
        }

        if (!$value instanceof Specification) {
            throw new TransformationFailedException(sprintf(
                'Invalid specification. Should be a Specification instance, but "%s" given.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $specification = $this->em->find(Specification::class, $value);

        if (!$specification) {
            throw new TransformationFailedException(sprintf(
                'Not found specification with identifier "%s".',
                $value
            ));
        }

        return $specification;
    }
}
