<?php

namespace Furniture\ProductBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProductVariantSkuModelTransformer implements DataTransformerInterface
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

        if (!$value instanceof ProductVariant) {
            throw new TransformationFailedException(sprintf(
                'Invalid product variant. Should be a ProductVariant instance, but "%s" instance.',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->getSku();
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $variant = $this->em->createQueryBuilder()
            ->from(ProductVariant::class, 'p')
            ->select('p')
            ->andWhere('p.sku = :sku')
            ->setParameter('sku', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$variant) {
            throw new TransformationFailedException(sprintf(
                'Not found product variant with identifier "%s".',
                $value
            ));
        }

        return $variant;
    }
}
