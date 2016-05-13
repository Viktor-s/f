<?php

namespace Furniture\SkuOptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;

class SkuOptionVariantFormType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('skuOptionType', 'entity', [
                'class' =>  SkuOptionType::class,
                'property' => 'name',
                'multiple' => false,
                'label' => 'Select Type',
                'disabled' => $options['disallow_edit'],
            ])
            ->add('value', 'text');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => SkuOptionVariant::class,
            'disallow_edit' => false,
        ));
    }
    
    public function getName() {
        return 'sku_option_value_form_type';
    }
    
}

