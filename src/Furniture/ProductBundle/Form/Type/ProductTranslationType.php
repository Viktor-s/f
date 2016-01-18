<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Core\Model\ProductTranslation;

class ProductTranslationType extends AbstractType 
{
    
    /**
     * {@inheritDoc}
    */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductTranslation::class
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.product.name'
            ))
            ->add('description',  'textarea', array(
                'attr' => array('class' => 'ckeditor')
                ) )
            ->add('shortDescription', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.short_description'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.meta_keywords'
            ))
            ->add('metaDescription', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.meta_description'
            ))
        ;
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_translation';
    }
    
}

