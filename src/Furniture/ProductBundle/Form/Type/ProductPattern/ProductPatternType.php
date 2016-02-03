<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Furniture\CommonBundle\Form\ModelTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductVariantsPattern::class
        ]);

        $resolver->setRequired(['product', 'scheme']);
        $resolver->setAllowedTypes('product', Product::class);
        $resolver->setAllowedTypes('scheme', ProductScheme::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', 'text', [
                'disabled' => true
            ])
            ->add('scheme', 'text', [
                'disabled' => true
            ])
            ->add('name', 'text', [
                'label' => 'Name'
            ]);

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());
        $builder->get('scheme')->addModelTransformer(new ObjectToStringTransformer());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pattern';
    }
}
