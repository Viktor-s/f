<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartMaterialVariantOptionsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product_part_material');
        $resolver->setAllowedTypes('product_part_material', ProductPartMaterial::class);

        $resolver->setRequired('product_part_material_variant');
        $resolver->setAllowedTypes('product_part_material_variant', ProductPartMaterialVariant::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPartMaterial $productExtension */
        $productExtension = $options['product_part_material'];
        /** @var ProductPartMaterialVariant $productExtensionVariant */
        $productExtensionVariant = $options['product_part_material_variant'];

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
                'class' => ProductPartMaterialOptionValue::class,
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
        return 'product_part_material_variant_options';
    }
}
