<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Product;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Type;
use Furniture\SkuOptionBundle\Form\Type\SkuOptionVariantFormType;
use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Furniture\CommonBundle\Form\Type\AutocompleteEntityType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'cascade_validation' => true,
            'validation_groups' => function (Form $form) {
                /** @var \Furniture\ProductBundle\Entity\Product $product */
                $product = $form->getData();
                $validationGroups = ['Default'];

                if ($product->getId()) {
                    $validationGroups = array_merge($validationGroups, ['Update']);
                    // Check that product schemes was added and validate it.
                    if ($product->isSchematicProductType()
                        && count($product->getProductParts()) > 1
                        && count($product->getProductSchemes()) > 0
                    ) {
                        $validationGroups = array_merge($validationGroups, ['SchemesCreate']);
                    }
                } else {
                    $validationGroups = array_merge($validationGroups, ['Create']);
                }

                return $validationGroups;
            },
            'mode' => 'full'
        ));

        $resolver->setAllowedValues([
            'mode' => ['small', 'full']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('translations')
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => new ProductTranslationType,
                'label'     => 'sylius.form.product.translations',
            ));
        
        if ($options['mode'] == 'full') {
            $builder
                ->add('categories', 'entity', [
                    'class'    => Category::class,
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('types', 'entity', [
                    'class'    => Type::class,
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('styles', 'entity', [
                    'class'    => Style::class,
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('spaces', 'entity', [
                    'class'    => Space::class,
                    'multiple' => true,
                    'expanded' => true,
                ])
                ->add('skuOptionVariants', 'collection', [
                    'type'         => new SkuOptionVariantFormType(),
                    'required'     => false,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label'        => 'product_options.sku_option_variants_label'
                ])
                ->add('subProducts', new AutocompleteEntityType(), [
                    'class'       => Product::class,
                    'property'    => 'name',
                    'source'      => 'furniture_autocomplete_for_none_bundle',
                    'placeholder' => 'Start type product name',
                    'multiple'    => true,
                ])
                ->add('availableForSale', 'checkbox', [
                    'required' => false
                ]);

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var Product $product */
                $product = $event->getData();
                $factory = $product->getFactory();

                $event->getForm()
                    ->add('productParts', 'collection', [
                        'type'         => new ProductPartFormType($factory),
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'attr'         => [
                            'data-remove-confirm' => 'Are you sure you want to remove product part item?',
                        ],
                    ])
                    ->add('compositeCollections', 'entity', [
                        'class' => CompositeCollection::class,
                        'query_builder' => function (EntityRepository $er) use ($factory) {
                            if ($factory) {
                                return $er->createQueryBuilder('cc')
                                    ->leftJoin('cc.factory', 'f')
                                    ->andWhere('f.id IS NULL OR f.id = :factory')
                                    ->setParameter('factory', $factory->getId());
                            } else {
                                return $er->createQueryBuilder('cc');
                            }
                        },
                        'multiple' => true,
                        'expanded' => false
                    ]);

                if (!$product->getId() || $product->isSchematicProductType() || count($product->getVariants())) {
                    $event->getForm()
                        ->add('productSchemes', new ProductSchemesType(), [
                            'parts' => $product->getProductParts(),
                            'schemes' => $product->getProductSchemes(),
                            'attr'  => [
                                'data-remove-confirm' => 'Are you sure you want to remove scheme item?',
                            ],
                        ]);
                }
            });

            // Add listener for save reference: ProductScheme -> Product
            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Product $product */
                $product = $event->getData();

                foreach ($product->getProductSchemes() as $productScheme) {
                    $productScheme->setProduct($product);
                }

                foreach ($product->getProductParts() as $productPart) {
                    $productPart->setProduct($product);
                }
            });

        } else if ($options['mode'] == 'small') {
            $builder
                ->add('availableForSale', 'checkbox', [
                    'required' => false,
                    'disabled' => true
                ])
                ->remove('options')
                ->remove('attributes');
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Product $product */
            $product = $event->getData();

            $hasVariants = $product->hasVariantsWithoutMaster();
            $hasSchemes = $product->hasProductSchemes();
            $hasVariantPatterns = $product->hasProductVariantsPatterns();

            $disabled = $hasVariants || $hasSchemes || $hasVariantPatterns;

            $event->getForm()->add('productType', 'choice', [
                'label' => 'Product type',
                'disabled' => $disabled,
                'choices' => [
                    Product::PRODUCT_SIMPLE => 'Simple',
                    Product::PRODUCT_SCHEMATIC => 'Schematic'
                ]
            ]);
        });

        $builder
            ->add('factoryCode')
            ->add('factory', 'entity', [
                'required' => false,
                'class'    => Factory::class,
                'multiple' => false,
                'property' => 'name',
                'disabled' => $options['mode'] == 'full'
            ]);

        // Remove taxons
        $builder->remove('taxons');
    }
}
