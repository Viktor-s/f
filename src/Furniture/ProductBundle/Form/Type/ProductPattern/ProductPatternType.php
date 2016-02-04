<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Doctrine\ORM\EntityRepository;
use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Form\Type\ProductPattern\DataTransformer\ProductPartPatternVariantCollectionTransformer;
use Furniture\ProductBundle\Form\Type\ProductPattern\DataTransformer\ProductSkuOptionCollectionTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductVariantsPattern::class
        ]);

        $resolver->setRequired(['product', 'parts', 'sku_options']);
        $resolver->setAllowedTypes('product', Product::class);
        $resolver->setAllowedTypes('parts', \Traversable::class);
        $resolver->setAllowedTypes('sku_options', \Traversable::class);
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
                'label' => 'Scheme',
                'disabled' => true
            ]);

            $builder->get('scheme')->addModelTransformer(new ObjectToStringTransformer());
        }

        $builder
            ->add('product', 'text', [
                'disabled' => true
            ])
            ->add('name', 'text', [
                'label' => 'Name'
            ])
            ->add('price', 'number', [
                'label' => 'Price'
            ])
            ->add('partPatternVariantSelections', new ProductPartPatternVariantCollectionType(), [
                'parts' => $options['parts']
            ])
            ->add('skuOptionValues', new ProductSkuOptionCollectionPatternType(), [
                'sku_options' => $options['sku_options']
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
            /** @var ProductVariantsPattern $pattern */
            $pattern = $event->getData();

            foreach ($pattern->getPartPatternVariantSelections() as $variantSelection) {
                $variantSelection->setPattern($pattern);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pattern';
    }
}
