<?php

namespace Furniture\FrontendBundle\Form\Type\Registration;

use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $builder
            ->add('retailerMode', 'hidden', [
                'data' => RetailerUserProfile::RETAILER_ADMIN,
            ])
            ->add('retailerProfile', 'furniture_retailer_profile', ['label' => false, 'registration' => true]);

        $builder
            ->add('phones', 'text', [
                'label' => 'Contact phones',
                'required' => false
            ])
            ->add('position', 'text', [
                'label' => 'Position',
                'required' => false
            ]);

        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_retailer_user_profile';
    }
}
