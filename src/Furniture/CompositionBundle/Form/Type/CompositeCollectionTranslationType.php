<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeCollectionTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeCollectionTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeCollectionTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation', 'text', [
                'label' => 'composite_collection.form.presentation'
            ])
            ->add('description', 'textarea', [
                'label' => 'composite_collection.form.description'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_collection_translation';
    }
}
