<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Furniture\ProductBundle\Entity\ProductPartPatternVariantSelection;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Form\Type\Pattern\PatternType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPatternType extends PatternType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ProductVariantsPattern::class,
            'variant_selection_class' => ProductPartPatternVariantSelection::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', 'sylius_money', [
                'label' => 'Price'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pattern';
    }
}
