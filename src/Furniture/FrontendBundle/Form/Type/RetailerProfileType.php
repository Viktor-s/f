<?php

namespace Furniture\FrontendBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Furniture\RetailerBundle\Form\Type\RetailerProfileType as BaseRetailerProfileType;

class RetailerProfileType extends BaseRetailerProfileType
{
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('logoImage')
            ;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_retailer_profile_frontend';
    }
}
