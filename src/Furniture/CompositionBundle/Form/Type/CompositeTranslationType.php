<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation', 'text', [
                'label' => 'composite.form.presentation'
            ])
            ->add('description', 'textarea', [
                'label' => 'composite.form.description'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_translation';
    }
}
