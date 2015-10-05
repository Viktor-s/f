<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariantImage;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        $builder
            ->add('descriptionalName', 'text', [
                'label' => 'Extension item name'
            ])
            ->add('descriptionalCode', 'text', [
                'label' => 'Extension item code '
            ])
            ->add('available', 'checkbox', [
                'label' => 'Available'
            ])
            ->add('image', new ImageType(ProductPartMaterialVariantImage::class), [
                'required' => false
            ]);

        $extensionVariant = $builder->getData();

        $builder->add('values', new ProductPartMaterialVariantOptionsType(), [
            'product_part_material' => $productExtension,
            'product_part_material_variant' => $extensionVariant
        ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var ProductPartMaterialVariant $materialVariant */
            $materialVariant = $event->getData();

            if ($materialVariant->getImage() && !$materialVariant->getImage()->getMaterialVariant()) {
                $materialVariant->getImage()->setMaterialVariant($materialVariant);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_material_variant';
    }
}
