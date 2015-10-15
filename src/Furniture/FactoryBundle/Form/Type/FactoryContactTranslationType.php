<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\FactoryContactTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryContactTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryContactTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'factory_contact.form.name'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory_contact_translation';
    }
}
