<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFactoryRateType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RetailerFactoryRate::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'label' => 'frontend.factory'
            ])
            ->add('coefficient', 'number', [
                'label' => 'frontend.coefficient'
            ])
            ->add('dumping', 'number', [
                'label' => 'frontend.dumping'
            ])
            ->add('_submit', 'submit', [
                'label' => 'frontend.save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_factory_rate';
    }
}
