<?php

namespace Furniture\SkuOptionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SkuOptionFormType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('translations') // @todo: why remove?
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new SkuOptionTranslationFormType('Furniture\SkuOptionBundle\Entity\SkuOptionTypeTranslation')
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'sku_option_form_type';
    }
}
