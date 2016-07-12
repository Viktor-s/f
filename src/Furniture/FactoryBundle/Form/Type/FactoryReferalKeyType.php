<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryReferalKey;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryReferalKeyType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryReferalKey::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => true
            ])
            ->add('enabled')
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory_referal_key';
    }
}
