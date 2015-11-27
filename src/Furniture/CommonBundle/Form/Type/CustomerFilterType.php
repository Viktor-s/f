<?php

namespace Furniture\CommonBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\Filter\CustomerFilterType as BaseCustomerFilterType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerFilterType extends BaseCustomerFilterType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('mode', 'choice', [
                'label' => 'Mode',
                'required' => true,
                'choices' => [
                    'all' => 'All',
                    'retailer' => 'Retailers'
                ]
            ])
            ->add('retailerId', 'integer');
    }
}
