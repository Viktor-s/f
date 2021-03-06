<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CommonBundle\Form\Type\BackendImageType;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\CompositionBundle\Entity\CompositeCollectionLogoImage;

class CompositeCollectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeCollection::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'composite_collection.form.name',
            ]) 
            ->add('factory')
            ->add('position', 'integer', [
                'label' => 'composite_collection.form.position',
            ])
            ->add('logoImage', new BackendImageType(CompositeCollectionLogoImage::class), [
                'required' => false
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new CompositeCollectionTranslationType()
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_collection';
    }
}
