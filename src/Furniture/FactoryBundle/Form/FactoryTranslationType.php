<?php

namespace Furniture\FactoryBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

use Symfony\Component\Form\FormBuilderInterface;

class FactoryTranslationType extends AbstractResourceType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => true
            ])
            ->add('description', 'textarea', array('attr' => array('class' => 'ckeditor')) )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory_translation';
    }
}
