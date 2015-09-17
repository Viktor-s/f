<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\CompositeTemplateItem;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeTemplateItemType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeTemplateItem::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxon', 'entity', [
                'class' => Taxon::class,
                'label' => 'composite_template_item.form.taxon'
            ])
            ->add('count', 'number', [
                'label' => 'composite_template_item.form.count_items',
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('position', 'number', [
                'label' => 'composite_template_item.form.position'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_template_item';
    }
}
