<?php

namespace Furniture\ProductBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\ProductBundle\Form\EventListener\BuildSkuOptionFormSubscriber;

class ProductVariantType extends BaseProductVariantType {
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $variant = $builder->getData();
        
        if (!$options['master']) {
            $builder->add('skuOptions', new ProductVariantSkuOptions($variant));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
    }
    
}

