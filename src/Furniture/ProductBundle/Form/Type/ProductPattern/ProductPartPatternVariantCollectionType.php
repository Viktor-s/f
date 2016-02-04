<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartPatternVariantCollectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('parts');
        $resolver->setAllowedTypes('parts', ['Traversable']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPart[] $parts */
        $parts = $options['parts'];

        foreach ($parts as $part) {
            $builder->add($part->getId(), new ProductPartPatternType(), [
                'part' => $part
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_pattern_variant_collection';
    }
}
