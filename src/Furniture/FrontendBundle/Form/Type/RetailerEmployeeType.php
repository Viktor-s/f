<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetailerEmployeeType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', [
                'label' => 'frontend.username',
            ])
            ->add('plainPassword', 'password', [
                'label' => 'frontend.password'
            ])
            ->add('retailerMode', 'choice', [
                'label' => 'frontend.mode',
                'choices' => [
                    User::RETAILER_ADMIN => 'Admin',
                    User::RETAILER_EMPLOYEE => 'Employee',
                ]
            ])
            ->add('customer', new RetailerEmployeeCustomerType());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee';
    }
}
