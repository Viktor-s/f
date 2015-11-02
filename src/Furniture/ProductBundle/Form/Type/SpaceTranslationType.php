<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\SpaceTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SpaceTranslation::class
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
        return 'product_space_translation';
    }
}
