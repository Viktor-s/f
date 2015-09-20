<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\CompositionBundle\Entity\CompositeTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Composite::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'composite.form.name',
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new CompositeTranslationType()
            ])
            ->add('images', 'collection', array(
                'type'         => new \Sylius\Bundle\CoreBundle\Form\Type\ImageType('Furniture\CompositionBundle\Entity\CompositeImage'),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ;

        // Add event listener for control collection field state (Enabled or disabled)
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Composite $composite */
            $composite = $event->getData();
            $collectionFieldDisabled = $composite && $composite->getId();

            $event->getForm()->add('template', 'entity', [
                'class' => CompositeTemplate::class,
                'label' => 'composite.form.template',
                'disabled' => $collectionFieldDisabled
            ]);

            if ($composite && $composite->getId() && $composite->getTemplate()) {
                $event->getForm()->add('products', new CompositeProductsType(), [
                    'composite' => $composite
                ]);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite';
    }
}
