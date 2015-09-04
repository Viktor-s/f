<?php

namespace Furniture\ProductBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductVariantType extends BaseProductVariantType {

    function __construct($dataClass, array $validationGroups, $variableName) {
        parent::__construct($dataClass, $validationGroups, $variableName);
        $this->dataClass = $dataClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
    }
    
}

