<?php

namespace Furniture\SkuOptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SkuOptionTranslationFormType extends AbstractResourceType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'textarea', array(
                'required' => true
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sku_option_translation_form_type';
    }
}
