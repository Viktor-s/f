<?php

namespace Furniture\RetailerBundle\Form\Type;

use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\CommonBundle\Form\ModelTransformer\ArrayToStringTransformer;
use Furniture\RetailerBundle\Entity\RetailerProfileLogoImage;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;

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
                ->add('logoImage', new ImageType(RetailerProfileLogoImage::class), [
                'required' => false
                ])
                ->add('address', 'text', [
                'required' => true
                ])
                ->add('phones', 'text', [
                'label' => 'furniture_retailer_profile.form.phones'
                ])
                ->add('emails', 'text', [
                'label' => 'furniture_retailer_profile.form.emails'
                ])
                ;
        
        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('emails')->addModelTransformer(new ArrayToStringTransformer(','));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_retailer_profile';
    }
    
}

