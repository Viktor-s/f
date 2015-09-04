<?php

namespace Furniture\SkuOptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkuOptionVariantFormType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        $builder
            ->add('skuOptionType', 'entity', [
                    'class' =>  'Furniture\SkuOptionBundle\Entity\SkuOptionType',
                    'property' => 'name',
                    'multiple' => false,
                    'label' => 'Select Type'
                ])
            ->add('value', 'text')
            ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Furniture\SkuOptionBundle\Entity\SkuOptionVariant',
        ));
    }
    
    public function getName() {
        return 'sku_option_value_form_type';
    }
    
}

