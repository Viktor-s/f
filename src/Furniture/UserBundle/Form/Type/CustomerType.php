<?php

namespace Furniture\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends BaseCustomerType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'mode' => null
        ]);

        $resolver->setAllowedValues([
            'mode' => [null, 'retailer']
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // Replace user form
        $builder
            ->add('user', 'sylius_user', [
                'mode' => $options['mode']
            ]);

        if ($options['mode'] == 'retailer') {
            $builder->remove('groups');
        }
    }
}
