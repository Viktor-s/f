<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\CompositionBundle\Entity\CompositeTemplate;
use Furniture\CompositionBundle\Entity\CompositeTemplateItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeTemplateType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeTemplate::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'composite_template.form.name'
            ])
            ->add('collection', 'entity', [
                'class' => CompositeCollection::class,
                'label' => 'composite_template.form.collection'
            ])
            ->add('items', 'collection', [
                'type' => new CompositeTemplateItemType(),
                'allow_add' => true,
                'allow_delete' => true
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var CompositeTemplate $template */
            $template = $event->getData();

            $template->getItems()->forAll(function ($index, CompositeTemplateItem $item = null) use ($template) {
                if ($item) {
                    $item->setTemplate($template);
                }

                return true;
            });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_template';
    }
}
