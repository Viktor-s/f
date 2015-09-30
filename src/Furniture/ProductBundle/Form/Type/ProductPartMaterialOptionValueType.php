<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartMaterialOption;
use Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartMaterialOptionValueType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPartMaterialOptionValue::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option', 'entity', [
                'class' => ProductPartMaterialOption::class,
                'label' => 'product_part_material_option_value.form.option'
            ])
            ->add('value', 'text', [
                'label' => 'product_part_material_option_value.form.value'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_material_option_value';
    }
}
