<?php

namespace Furniture\ProductBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\CommonBundle\Form\Type\AutocompleteEntityType;

class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
                'subProducts', 
                new AutocompleteEntityType(), [
                    'class' =>  'Furniture\ProductBundle\Entity\Product',
                    'property' => 'name',
                    'source' => 'furniture_autocomplete_for_none_bundle',
                    'placeholder' => 'Start type product name',
                    'multiple' => true
                ])
            ->add('factoryCode')
            ->add('skuOptionVariants', 'collection', [
                    'type' => new \Furniture\SkuOptionBundle\Form\Type\SkuOptionVariantFormType(),
                    'required'  => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
            ;
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ));
    }
    
}
