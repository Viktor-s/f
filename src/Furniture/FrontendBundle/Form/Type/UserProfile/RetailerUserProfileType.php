<?php

namespace Furniture\FrontendBundle\Form\Type\UserProfile;

use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('position', 'text', [
                'label' => 'frontend.position',
                'required' => false
            ])
            ->add('phones', 'text', [
                'label' => 'frontend.phones_contact',
                'required' => false
            ]);
        
        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_user_profile';
    }
}
