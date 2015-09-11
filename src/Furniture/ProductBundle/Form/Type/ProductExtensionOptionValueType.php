<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductExtensionOption;
use Furniture\ProductBundle\Entity\ProductExtensionOptionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductExtensionOptionValueType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductExtensionOptionValue::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option', 'entity', [
                'class' => ProductExtensionOption::class,
                'label' => 'product_extension_option_value.form.option'
            ])
            ->add('value', 'text', [
                'label' => 'product_extension_option_value.form.value'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_extension_option_value';
    }
}
