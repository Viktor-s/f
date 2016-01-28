<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\Readiness;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadinessType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Readiness::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Name',
            ])
            ->add('position', 'number', [
                'label' => 'Position',
                'attr'  => [
                    'value' => 0,
                ],
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_readiness';
    }
}
