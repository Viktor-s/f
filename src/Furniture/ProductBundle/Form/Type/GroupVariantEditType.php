<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\ProductBundle\Model\GroupVaraintEdit;

class GroupVariantEditType extends GroupVariantFilterType {
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GroupVaraintEdit::class,
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->add('priceCalculator', 'text', [
            'label' => 'Change cost',
            'required' => false,
        ])
             ->add('factoryCodeUpdate', 'text', [
            'label' => 'Change factory code',
                'required' => false,
            ])
            ->add('width', 'text', [
            'label' => 'Change width',
                'required' => false,
        ])
            ->add('height', 'text', [
            'label' => 'Change height',
                'required' => false,
        ])
            ->add('depth', 'text', [
            'label' => 'Change depth',
                'required' => false,
        ])
            ->add('weight', 'text', [
            'label' => 'Change weight',
                'required' => false,
        ])
            ->add('delete_by_filter', 'submit', [
            'label' => 'Delete this items',
            'attr' => ['class' => 'btn btn-danger btn-md']
        ])
            ;
        
    }
    
    public function getName() {
        return 'group_variant_edit_type';
    }
    
}

