<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductExtension;
use Furniture\ProductBundle\Entity\ProductExtensionVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductExtensionVariantType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductExtensionVariant::class
        ]);

        $resolver->setRequired('product_extension');
        $resolver->setAllowedTypes('product_extension', ProductExtension::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductExtension $productExtension */
        $productExtension = $options['product_extension'];

        $builder->add('available', 'checkbox', [
            'label' => 'Available'
        ]);

        $extensionVariant = $builder->getData();

        $builder->add('values', new ProductExtensionVariantOptionsType(), [
            'product_extension' => $productExtension,
            'product_extension_variant' => $extensionVariant
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_extension_variant';
    }
}
