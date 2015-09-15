<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductExtensionVariant;
use Furniture\SkuOptionBundle\Form\Type\SkuOptionVariantFormType;
use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\CommonBundle\Form\Type\AutocompleteEntityType;

class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('subProducts', new AutocompleteEntityType(), [
                'class' =>  Product::class,
                'property' => 'name',
                'source' => 'furniture_autocomplete_for_none_bundle',
                'placeholder' => 'Start type product name',
                'multiple' => true
            ])
            ->add('factoryCode')
            ->add('skuOptionVariants', 'collection', [
                'type' => new SkuOptionVariantFormType(),
                'required'  => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('extensionVariants', 'entity', [
                'class' => ProductExtensionVariant::class,
                'multiple' => true,
                'expanded' => false
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ));
    }
}
