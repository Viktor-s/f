<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Furniture\ProductBundle\Entity\ProductPartType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartTypeType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPartType::class
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', [
                'label' => 'product_part_material.form.name'
            ])
        ;

    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_type';
    }
    
}

