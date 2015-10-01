<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Furniture\ProductBundle\Entity\ProductPartType;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPartFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPart::class
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new ProductPartTranslationFormType(),
                'empty_data' => function($form){
                    return new ArrayCollection;
                },
            ])
            ->add('productPartMaterials', 'entity', [
                'class' => ProductPartMaterial::class,
                'multiple' => true,
                'expanded' => false
            ])
            ->add('prtoductPartType', 'entity', [
                'class' => ProductPartType::class,
                'multiple' => false
            ])
        ;

    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part';
    }
    
}

