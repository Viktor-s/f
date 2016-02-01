<?php

namespace Furniture\RetailerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RetailerProfileFilterType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_filter';
    }
}
