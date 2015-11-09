<?php

namespace Furniture\FrontendBundle\Form\Type;

use Sylius\Component\Core\Model\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetailerEmployeeCustomerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'frontend.email'
            ])
            ->add('firstName', 'text', [
                'label' => 'frontend.first_name'
            ])
            ->add('lastName', 'text', [
                'label' => 'frontend.last_name'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee_customer';
    }
}
