<?php

namespace Furniture\ProductBundle\Form\Type\Pattern;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartPatternVariantCollectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['parts', 'variant_selection_class']);
        $resolver->setAllowedTypes('parts', \Traversable::class);
        $resolver->setAllowedTypes('variant_selection_class', 'string');

    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPart[] $parts */
        $parts = $options['parts'];

        foreach ($parts as $part) {
            $builder->add($part->getId(), new PartPatternType(), [
                'variant_selection_class' => $options['variant_selection_class'],
                'part'                    => $part,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'part_pattern_variant_collection';
    }
}
