<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\CompositionBundle\Form\EventListener\CompositeProductPopulatorSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeProductsType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('composite');
        $resolver->setAllowedTypes('composite', Composite::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Composite $composite */
        $composite = $options['composite'];
        $builder->addEventSubscriber(new CompositeProductPopulatorSubscriber($composite));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_products';
    }
}
