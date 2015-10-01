<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPartTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartTranslationFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPartTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', 'text', [
                'label' => 'product_part.form.presentation'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_translation';
    }
}

