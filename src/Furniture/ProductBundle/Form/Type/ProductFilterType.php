<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Bundle\CoreBundle\Form\Type\Filter\ProductFilterType as BaseProductFilterType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductFilterType extends BaseProductFilterType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => false,
                'label'    => 'sylius.form.product_filter.name',
                'attr'     => [
                    'placeholder' => 'sylius.form.product_filter.name',
                ],
            ])
            ->add('factory', 'entity', [
                'class'    => Factory::class,
                'required' => false,
                'label'    => 'sylius.form.product_filter.factory',
                'attr'     => [
                    'placeholder' => 'sylius.form.product_filter.factory',
                ],
            ])
            ->add('factoryCode', 'text', [
                'required' => false,
                'label'    => 'sylius.form.product_filter.factory_code',
                'attr'     => [
                    'placeholder' => 'sylius.form.product_filter.factory_code',
                ],
            ])
            ->add('priceFrom', 'number', [
                'required' => false,
                'label'    => 'sylius.form.product_filter.price_from',
                'attr'     => [
                    'placeholder' => 'sylius.form.product_filter.price_from',
                    'min'         => 0,
                    'style'       => 'width: 100px',
                ],
            ])
            ->add('priceTo', 'number', [
                'required' => false,
                'label'    => 'sylius.form.product_filter.price_to',
                'attr'     => [
                    'placeholder' => 'sylius.form.product_filter.price_to',
                    'min'         => 0,
                    'style'       => 'width: 100px',
                ],
            ]);
    }
}
