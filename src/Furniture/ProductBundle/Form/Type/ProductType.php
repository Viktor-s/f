<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Product;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\Readiness;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Type;
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
            ->add('compositeCollections', 'entity', [
                'class' => CompositeCollection::class,
                'multiple' => true,
                'expanded' => false
            ])
            ->add('factory', 'entity', [
                'required' => true,
                'class'    => Factory::class,
                'multiple' => false,
                'property' => 'name',
            ])
            ->add('productParts', 'collection', [
                'type' => new ProductPartFormType(),
                'required'  => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('categories', 'entity', [
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('types', 'entity', [
                'class' => Type::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('styles', 'entity', [
                'class' => Style::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('spaces', 'entity', [
                'class' => Space::class,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('readinesses', 'entity', [
                'class' => Readiness::class,
                'multiple' => true,
                'expanded' => true
            ]);

        // Replace taxons
        $builder->remove('taxons');

//        $builder->add('taxons', 'sylius_taxon_selection', [
//            'expanded' => true
//        ]);
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
