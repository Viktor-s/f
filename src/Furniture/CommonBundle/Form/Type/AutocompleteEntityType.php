<?php

namespace Furniture\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class AutocompleteEntityType extends AbstractType {
    
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults(array(
            'source' => '',
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options){
        $view->vars['source'] = $options['source'];
    }
    
    public function getParent(){
        return 'entity';
    }
    
    public function getName(){
        return 'AutocompleteEntity';
    }

}


