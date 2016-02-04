<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetailerProfileType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RetailerProfile::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => true
            ])
            ->add('website', 'text', [
                'required' => false
            ])
            ->add('subtitle', 'text', [
                'required' => false
            ])
            ->add('description', 'textarea', [
                'required' => false
            ])
            ->add('address', 'text', [
                'attr' => array('class'=>'google-address-autocomplete'),
                'required' => false
            ])
            ->add('phones', 'text', [
                'label' => 'furniture_retailer_profile.form.phones',
                'required' => false
            ])
            ->add('emails', 'text', [
                'label' => 'furniture_retailer_profile.form.emails',
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
        return 'furniture_retailer_profile_frontend';
    }
}
