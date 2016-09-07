<?php

namespace Furniture\ProductBundle\Form\Type;


use Furniture\CommonBundle\Form\Type\AutocompleteEntityType;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\BestSellers;
use Furniture\ProductBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BestSellersType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => BestSellers::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'product',
                new AutocompleteEntityType(),
                [
                    'class'       => Product::class,
                    'property'    => 'name',
                    'source'      => 'furniture_autocomplete_related',
                    'placeholder' => 'Start type product name',
                    'multiple'    => false,
                ]
            )
            ->add('position', null, [
                'label' => 'product_best_sellers.form.position'
            ]);
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_best_sellers';
    }
}