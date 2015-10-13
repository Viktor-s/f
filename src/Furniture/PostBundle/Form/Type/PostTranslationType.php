<?php

namespace Furniture\PostBundle\Form\Type;

use Furniture\PostBundle\Entity\PostTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PostTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Title'
            ])
            ->add('shortDescription', 'textarea', [
                'label' => 'Short description'
            ])
            ->add('content', 'textarea', [
                'label' => 'Content',
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
        return 'post_translation';
    }
}
