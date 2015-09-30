<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartMaterialVariantType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPartMaterialVariant::class
        ]);

        $resolver->setRequired('product_part_material');
        $resolver->setAllowedTypes('product_part_material', ProductPartMaterial::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPartMaterial $productExtension */
        $productExtension = $options['product_part_material'];

        $builder->add('descriptionalName', 'text', [
            'label' => 'Extension item name'
        ]);
        
        $builder->add('descriptionalCode', 'text', [
            'label' => 'Extension item code '
        ]);

        $builder->add('available', 'checkbox', [
            'label' => 'Available'
        ]);

        $extensionVariant = $builder->getData();

        $builder->add('values', new ProductPartMaterialVariantOptionsType(), [
            'product_part_material' => $productExtension,
            'product_part_material_variant' => $extensionVariant
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_material_variant';
    }
}
