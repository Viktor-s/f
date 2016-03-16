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
                'required' => true,
                'label'    => 'frontend.name',
            ])
            ->add('website', 'text', [
                'required' => false,
                'label'    => 'frontend.website',
            ])
            ->add('subtitle', 'text', [
                'required' => false,
                'label'    => 'frontend.subtitle',
            ])
            ->add('description', 'textarea', [
                'required' => false,
                'label' => 'frontend.description'
            ])
            ->add('phones', 'text', [
                'label' => 'frontend.phones_contact',
                'required' => false
            ])
            ->add('emails', 'text', [
                'label' => 'frontend.emails_contact',
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
