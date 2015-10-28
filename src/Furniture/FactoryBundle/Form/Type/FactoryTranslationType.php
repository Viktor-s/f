<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\FactoryTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')) )
            ->add('shortDescription', 'textarea')
            ->add('address')
            ->add('workInfoContent', 'textarea', [
                'label' => 'Work info',
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ])
            ->add('collectionContent', 'textarea', [
                'label' => 'Collections',
                'attr' => [
                    'class' => 'ckeditor'
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory_translation';
    }
}
