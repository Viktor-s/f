<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\CategoryTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategoryTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Name'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_category_translation';
    }
}
