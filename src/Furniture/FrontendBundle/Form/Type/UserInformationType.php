<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\FrontendBundle\Form\Type\RetailerEmployee\RetailerEmployeeUserProfileType;
use Symfony\Component\Form\AbstractType;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInformationType extends AbstractType
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
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'property_path' => 'customer.email',
                'label' => 'frontend.email'
            ])
            ->add('firstName', 'text', [
                'property_path' => 'customer.firstName',
                'label' => 'frontend.first_name'
            ])
            ->add('lastName', 'text', [
                'property_path' => 'customer.lastName',
                'label' => 'frontend.last_name',
                'required' => false
            ])
            ->add('retailerUserProfile', new RetailerEmployeeUserProfileType());
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'frontend_user_profile_type';
    }
}

