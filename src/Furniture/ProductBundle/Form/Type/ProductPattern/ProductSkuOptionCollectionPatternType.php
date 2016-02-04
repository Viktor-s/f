<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSkuOptionCollectionPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('sku_options');
        $resolver->setAllowedTypes('sku_options', \Traversable::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\SkuOptionBundle\Entity\SkuOptionVariant[] $skuOptions */
        $skuOptions = $options['sku_options'];

        // Group sku option variants by name
        $grouped = [];

        foreach ($skuOptions as $skuOption) {
            $name = $skuOption->getName();

            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'name'    => $name,
                    'options' => new ArrayCollection(),
                ];
            }

            $grouped[$name]['options'][] = $skuOption;
        }

        // Add child forms
        foreach ($grouped as $groupInfo) {
            $name = $groupInfo['name'];
            $skuOptions = $groupInfo['options'];

            $builder->add($name, new ProductSkuOptionGroupType(), [
                'name'        => $name,
                'sku_options' => $skuOptions,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_sku_option_collection_pattern';
    }
}
