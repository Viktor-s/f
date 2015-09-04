<?php

namespace Furniture\SkuOptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkuOptionFormType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        $builder
            ->remove('translations')
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new \Furniture\SkuOptionBundle\Form\Type\SkuOptionTranslationFormType(
                        'Furniture\SkuOptionBundle\Entity\SkuOptionTypeTranslation')
            ])
        
                ;
    }
    
    public function getName() {
        return 'sku_option_form_type';
    }

}

