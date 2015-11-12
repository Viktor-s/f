<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
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
            ->add('position', 'integer', [
                'label' => 'composite_collection.form.position',
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new CompositeCollectionTranslationType()
            ])
            ->add('logoImage', new ImageType(CompositeCollectionLogoImage::class), [
                'required' => false
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
