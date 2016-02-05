<?php

namespace Furniture\ProductBundle\Form\Type\Pattern;

use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Form\Type\Pattern\DataTransformer\ProductPartPatternVariantCollectionTransformer;
use Furniture\ProductBundle\Form\Type\Pattern\DataTransformer\ProductSkuOptionCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class PatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['product', 'parts', 'sku_options', 'variant_selection_class']);
        $resolver->setAllowedTypes('product', Product::class);
        $resolver->setAllowedTypes('parts', \Traversable::class);
        $resolver->setAllowedTypes('sku_options', \Traversable::class);
        $resolver->setAllowedTypes('variant_selection_class', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Product $product */
        $product = $options['product'];

        if ($product->isSchematicProductType()) {
            // Add event listener for inject scheme type.
            $builder->add('scheme', 'text', [
                'label'    => 'Scheme',
                'disabled' => true,
            ]);

            $builder->get('scheme')->addModelTransformer(new ObjectToStringTransformer());
        }

        $builder
            ->add('product', 'text', [
                'disabled' => true,
            ])
            ->add('name', 'text', [
                'label' => 'Name',
            ])
            ->add('partPatternVariantSelections', new PartPatternVariantCollectionType(), [
                'parts'                   => $options['parts'],
                'variant_selection_class' => $options['variant_selection_class'],
            ])
            ->add('skuOptionValues', new SkuOptionCollectionPatternType(), [
                'sku_options' => $options['sku_options'],
            ]);

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());

        $builder->get('partPatternVariantSelections')->addModelTransformer(
            new ProductPartPatternVariantCollectionTransformer($options['parts'])
        );

        $builder->get('skuOptionValues')->addModelTransformer(
            new ProductSkuOptionCollectionTransformer($options['sku_options'])
        );

        // Add event listener for save reference to pattern
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var \Furniture\ProductBundle\Entity\Pattern\AbstractProductVariantsPattern $pattern */
            $pattern = $event->getData();

            foreach ($pattern->getPartPatternVariantSelections() as $variantSelection) {
                $variantSelection->setPattern($pattern);
            }
        });
    }
}

// Furniture\ProductBundle\Entity\ProductPartVariantSelection
// Furniture\ProductBundle\Entity\ProductPartPatternVariantSelection