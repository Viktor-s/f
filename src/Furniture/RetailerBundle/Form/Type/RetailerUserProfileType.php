<?php

namespace Furniture\RetailerBundle\Form\Type;

use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerUserProfileType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RetailerUserProfile::class
        ]);
    }
    
     /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('retailerMode', 'choice', [
                'label' => 'Retailer mode',
                'required' => false,
                'choices' => [
                    RetailerUserProfile::RETAILER_ADMIN => 'Admin',
                    RetailerUserProfile::RETAILER_EMPLOYEE => 'Manager'
                ]
            ])
            ->add('retailerProfile', 'entity', [
                'class' => RetailerProfile::class,
                'multiple' => false,
                'required' => false
            ])
            ->add('emails', 'text', [
                'label' => 'Contact emails',
                'required' => false
            ])
            ->add('phones', 'text', [
                'label' => 'Contact phones',
                'required' => false
            ])
            ->add('position', 'text', [
                'label' => 'Position',
                'required' => false
            ]);

        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('emails')->addModelTransformer(new ArrayToStringTransformer(','));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_retailer_user_profile';
    }
}
