<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\FactoryBundle\Entity\FactoryDefaultRelation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryDefaultRelationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryDefaultRelation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accessProducts', 'checkbox', [
                'label' => 'frontend.products_view',
                'required' => false
            ])
            ->add('accessProductsPrices', 'checkbox', [
                'label' => 'frontend.product_prices_view',
                'required' => false
            ])
            ->add('_submit', 'submit', [
                'label' => 'frontend.save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory_default_relation';
    }
}
