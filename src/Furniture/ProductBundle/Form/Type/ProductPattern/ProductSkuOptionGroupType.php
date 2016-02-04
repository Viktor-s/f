<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Furniture\CommonBundle\Form\DataTransformer\CheckboxForValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSkuOptionGroupType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['name', 'sku_options']);
        $resolver->setAllowedTypes('sku_options', \Traversable::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\SkuOptionBundle\Entity\SkuOptionVariant[] $skuOptions */
        $skuOptions = $options['sku_options'];

        foreach ($skuOptions as $skuOption) {
            $innerBuilder = $builder->getFormFactory()->createNamedBuilder($skuOption->getId(), 'checkbox', null, [
                'label' => $skuOption->getValue()
            ]);

            $innerBuilder->addModelTransformer(new CheckboxForValueTransformer($skuOption));

            $builder->add($innerBuilder);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['group_name'] = $options['name'];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_sku_option_group';
    }
}
