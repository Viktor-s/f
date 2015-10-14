<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductExtensionVariant;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductVariantExtensionVariantsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product_variant');
        $resolver->setAllowedTypes('product_variant', ProductVariant::class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductVariant $productVariant */
        $productVariant = $options['product_variant'];
        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $productVariant->getProduct();

        $extensions = $product->getMaterials();

        $i = 0;
        foreach ($extensions as $extension) {
            $builder->add($i, 'entity', [
                'class' => ProductExtensionVariant::class,
                'label' => $extension->getName(),
                'choices' => $extension->getVariants(),
                'required' => false
            ]);

            $i++;
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var \Doctrine\Common\Collections\Collection $data */
            $data = $event->getData();

            $filtered = $data->filter(function (ProductExtensionVariant $variant = null) {
                return $variant ? true : false;
            });

            $event->setData($filtered);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_variant_extension_variant';
    }
}
