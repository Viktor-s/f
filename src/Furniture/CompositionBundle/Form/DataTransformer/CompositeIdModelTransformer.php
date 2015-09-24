<?php

namespace Furniture\CompositionBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Entity\Composite;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CompositeIdModelTransformer implements DataTransformerInterface
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

        if (!$value instanceof Composite) {
            throw new TransformationFailedException(sprintf(
                'Invalid composite. Should be a Composite instance, but "%s" given.',
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

        $composite = $this->em->find(Composite::class, $value);

        if (!$composite) {
            throw new TransformationFailedException(sprintf(
                'Not found composite with identifier "%s".',
                $value
            ));
        }

        return $composite;
    }
}
