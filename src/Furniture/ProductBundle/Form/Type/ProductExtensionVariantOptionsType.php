<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductExtension;
use Furniture\ProductBundle\Entity\ProductExtensionOptionValue;
use Furniture\ProductBundle\Entity\ProductExtensionVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductExtensionVariantOptionsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product_extension');
        $resolver->setAllowedTypes('product_extension', ProductExtension::class);

        $resolver->setRequired('product_extension_variant');
        $resolver->setAllowedTypes('product_extension_variant', ProductExtensionVariant::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductExtension $productExtension */
        $productExtension = $options['product_extension'];
        /** @var ProductExtensionVariant $productExtensionVariant */
        $productExtensionVariant = $options['product_extension_variant'];

        $data = [];

        foreach ($productExtensionVariant->getValues() as $variantValue) {
            $id = $variantValue->getOption()->getId();
            $data[$id] = $variantValue;
        }

        $groupedOptionValues = $productExtension->getGroupedOptionValues();
        $i = 0;

        foreach ($groupedOptionValues as $optionValues) {
            $name = $optionValues->getName();
            $values = $optionValues->getValues();
            $id = $optionValues->getOption()->getId();

            $builder->add($i, 'entity', [
                'class' => ProductExtensionOptionValue::class,
                'label' => $name,
                'choices' => $values,
                'data' => isset($data[$id]) ? $data[$id] : null
            ]);

            $i++;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_extension_variant_options';
    }
}
